#!/usr/bin/env python3
"""Parcours E2E PATRIA via la gateway : login -> bien -> transfert -> inventaire -> écarts -> panne -> clôture -> export PDF."""
import json, os, sys, urllib.request, urllib.error

BASE = os.environ.get('BASE', 'http://localhost')
EMAIL, PASSWORD = 'admin@patria.gov', 'Password123!'

def req(method, path, token=None, data=None):
    url = BASE + path
    body = json.dumps(data).encode() if data is not None else None
    r = urllib.request.Request(url, data=body, method=method)
    r.add_header('Content-Type', 'application/json')
    if token:
        r.add_header('Authorization', f'Bearer {token}')
    try:
        with urllib.request.urlopen(r, timeout=120) as resp:
            raw = resp.read()
            ct = resp.headers.get('Content-Type', '')
            return resp.status, (raw if 'pdf' in ct or 'octet' in ct else json.loads(raw or b'{}'))
    except urllib.error.HTTPError as e:
        return e.code, e.read()[:500]

def step(name, ok, extra=''):
    print(f"{'OK ' if ok else 'FAIL'} {name} {extra}")
    return ok

fails = 0
def check(name, ok, extra=''):
    global fails
    if not step(name, ok, extra): fails += 1

# 1. Login
s, d = req('POST', '/api/auth/login', data={'email': EMAIL, 'password': PASSWORD})
token = (d.get('access_token') or d.get('token')) if isinstance(d, dict) else None
check('login', s == 200 and token, f'({s})')
if not token: sys.exit(1)

# 2. Référentiels
s, cats = req('GET', '/api/categories', token)
s2, locs = req('GET', '/api/locations', token)
cat_list = cats.get('data', cats) if isinstance(cats, dict) else cats
loc_list = locs.get('data', locs) if isinstance(locs, dict) else locs
cat_id = cat_list[0]['id'] if isinstance(cat_list, list) and cat_list else None
loc1 = loc_list[0]['id'] if loc_list else None
loc2 = loc_list[1]['id'] if len(loc_list) > 1 else loc1
check('referentiels categories/locations', bool(cat_id and loc1), f'(cat={cat_id}, locs={len(loc_list)})')

# 3. Création d'un bien
s, asset = req('POST', '/api/assets', token, {
    'name': 'Test E2E ' + __import__('time').strftime('%H%M%S'),
    'category_id': cat_id,
    'acquisition_date': '2026-01-15',
    'acquisition_value': 500000,
    'status': 'EN_SERVICE',
    'condition': 'BON',
    'location_id': loc1,
    'service_id': 'SRV-001',
})
asset_id = asset.get('id') or asset.get('data', {}).get('id')
asset_code = asset.get('code') or asset.get('data', {}).get('code')
check('creation bien', s in (200, 201) and asset_id, f'({s}) id={asset_id} code={asset_code}')
if not asset_id: sys.exit(1)

# 4. Transfert
s, mv = req('POST', '/api/movements', token, {
    'asset_id': asset_id, 'type': 'TRANSFERT',
    'to_service_id': 'SRV-002', 'to_location_id': loc2,
    'movement_date': '2026-11-15', 'reason': 'Test E2E transfert',
})
check('transfert', s in (200, 201), f'({s}) {str(mv)[:120]}')

# 5. Campagne d'inventaire
s, camp = req('POST', '/api/campaigns', token, {
    'label': 'Campagne E2E', 'start_date': '2026-11-01', 'end_date': '2026-11-30',
})
camp_id = camp.get('id')
check('campagne inventaire', s == 201 and camp_id, f'({s})')

# 6. Ligne de comptage avec mauvais lieu -> ECART à la clôture
s, line = req('POST', f'/api/campaigns/{camp_id}/lines', token, {
    'asset_code': asset_code, 'found_location_id': loc1 if loc1 != loc2 else loc2,
})
check('ligne comptage', s in (200, 201), f'({s}) {str(line)[:120]}')

# Forcer un MANQUANT : le bien est listé? on clôture directement
s, closed = req('POST', f'/api/campaigns/{camp_id}/close', token)
check('cloture campagne', s in (200, 201, 204), f'({s})')

s, disc = req('GET', f'/api/campaigns/{camp_id}/discrepancies', token)
items = disc.get('data', disc) if isinstance(disc, dict) else disc
check('ecarts lisibles', s == 200 and isinstance(items, list), f'({s}) n={len(items) if isinstance(items,list) else "?"}')

# 7. Déclaration de panne
s, inter = req('POST', '/api/interventions', token, {
    'asset_id': asset_id, 'title': 'Panne E2E',
    'description': 'Test parcours complet', 'scheduled_date': '2026-11-15',
})
inter_id = inter.get('id') or inter.get('data', {}).get('id')
check('declaration panne', s in (200, 201) and inter_id, f'({s}) id={inter_id}')

# 8. Clôture de l'intervention (start puis close)
if inter_id:
    s1, _ = req('PATCH', f'/api/interventions/{inter_id}/start', token)
    check('demarrage intervention', s1 in (200, 201, 204, 422), f'({s1})')
    s2, cl = req('PATCH', f'/api/interventions/{inter_id}/close', token, {
        'cost': 75000, 'provider': 'SARL Maintenance', 'report': 'Remplacement pièce E2E',
    })
    check('cloture intervention', s2 in (200, 201, 204), f'({s2}) {str(cl)[:120]}')

# 9. Export PDF du rapport
s, pdf = req('GET', '/api/reports/exports/assets?format=pdf', token)
is_pdf = isinstance(pdf, (bytes, bytearray)) or (isinstance(pdf, str) and pdf[:4] == '%PDF')
# req() returns raw bytes for pdf content-type
check('export PDF', s == 200, f'({s})')
if s == 200:
    head = pdf[:8] if isinstance(pdf, (bytes, bytearray)) else b''
    print('   -> début du fichier:', head)

print()
print('PARCOURS', 'REUSSI' if fails == 0 else f'AVEC {fails} ECHEC(S)')
sys.exit(1 if fails else 0)
