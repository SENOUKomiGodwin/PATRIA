import axios from 'axios';
import Alpine from 'alpinejs';
import QRCode from 'qrcode';

window.axios = axios;
window.Alpine = Alpine;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Alpine.data('qrCode', (code) => ({
    svg: '',
    init() {
        QRCode.toString(code, {
            type: 'svg',
            width: 136,
            margin: 0,
            color: {
                dark: '#0C6744',
                light: '#FFFFFF00'
            }
        })
        .then(svg => { this.svg = svg; })
        .catch(console.error);
    }
}));

Alpine.start();
