/**
 * ==========================================================================
 * POSHUB ACCOUNTING - HARDWARE NATIVE BRIDGES
 * Direct Bluetooth Thermal Printer, Camera Barcode Scanner & Sunmi POS SDK
 * ==========================================================================
 */

(function () {
    'use strict';

    // --------------------------------------------------------------------------
    // 1. DIRECT BLUETOOTH THERMAL PRINTER (ESC/POS)
    // --------------------------------------------------------------------------
    class BluetoothThermalPrinter {
        constructor() {
            this.device = null;
            this.server = null;
            this.characteristic = null;
            this.isConnected = false;
        }

        async connect() {
            if (!navigator.bluetooth) {
                throw new Error('Web Bluetooth API tidak didukung pada browser ini. Gunakan Chrome / Edge di Android/PC atau Capacitor Native App.');
            }

            try {
                this.device = await navigator.bluetooth.requestDevice({
                    filters: [
                        { services: ['000018f0-0000-1000-8000-00805f9b34fb'] },
                        { services: ['e7810a71-73ae-499d-8c15-faa9aef0c3f2'] },
                        { services: ['49535343-fe7d-4ae5-8fa9-9fafd205e455'] }
                    ],
                    optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb', 'e7810a71-73ae-499d-8c15-faa9aef0c3f2', '49535343-fe7d-4ae5-8fa9-9fafd205e455']
                });

                this.server = await this.device.gatt.connect();
                const services = await this.server.getPrimaryServices();
                
                for (const service of services) {
                    const characteristics = await service.getCharacteristics();
                    for (const char of characteristics) {
                        if (char.properties.write || char.properties.writeWithoutResponse) {
                            this.characteristic = char;
                            break;
                        }
                    }
                    if (this.characteristic) break;
                }

                if (!this.characteristic) {
                    throw new Error('Tidak dapat menemukan characteristic tulis pada printer Bluetooth.');
                }

                this.isConnected = true;
                console.log('[POSHUB BLE] Terhubung ke printer Bluetooth:', this.device.name);
                return { success: true, deviceName: this.device.name };
            } catch (err) {
                console.error('[POSHUB BLE] Gagal terhubung ke printer Bluetooth:', err);
                this.isConnected = false;
                throw err;
            }
        }

        async printRaw(byteArray) {
            if (!this.isConnected || !this.characteristic) {
                await this.connect();
            }

            const chunkSize = 100; // Send in 100-byte chunks to prevent buffer overflow
            for (let i = 0; i < byteArray.length; i += chunkSize) {
                const chunk = byteArray.slice(i, i + chunkSize);
                await this.characteristic.writeValue(chunk);
            }
            console.log('[POSHUB BLE] Cetak data ESC/POS berhasil dikirim.');
        }

        async printReceiptText(receiptText) {
            const encoder = new TextEncoder();
            // ESC @ (Initialize) + text + GS V 0 (Cut Paper)
            const initCmd = new Uint8Array([0x1B, 0x40]);
            const cutCmd = new Uint8Array([0x0A, 0x0A, 0x0A, 0x1D, 0x56, 0x00]);
            const textBytes = encoder.encode(receiptText);

            const combined = new Uint8Array(initCmd.length + textBytes.length + cutCmd.length);
            combined.set(initCmd, 0);
            combined.set(textBytes, initCmd.length);
            combined.set(cutCmd, initCmd.length + textBytes.length);

            return this.printRaw(combined);
        }
    }

    // --------------------------------------------------------------------------
    // 2. CAMERA BARCODE & QR CODE SCANNER (ML KIT / NATIVE BARCODE DETECTOR)
    // --------------------------------------------------------------------------
    class CameraBarcodeScanner {
        constructor() {
            this.videoEl = null;
            this.stream = null;
            this.isScanning = false;
            this.detector = null;
        }

        async isSupported() {
            return 'BarcodeDetector' in window;
        }

        async start(videoElementId, onDetectedCallback) {
            this.videoEl = document.getElementById(videoElementId);
            if (!this.videoEl) throw new Error('Video container element tidak ditemukan.');

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                this.videoEl.srcObject = this.stream;
                await this.videoEl.play();
                this.isScanning = true;

                if ('BarcodeDetector' in window) {
                    this.detector = new window.BarcodeDetector({
                        formats: ['qr_code', 'ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a']
                    });
                    this._scanLoop(onDetectedCallback);
                } else {
                    console.warn('[POSHUB Scanner] Native BarcodeDetector tidak aktif, mengaktifkan video preview.');
                }
            } catch (err) {
                console.error('[POSHUB Scanner] Gagal mengakses kamera:', err);
                throw err;
            }
        }

        async _scanLoop(callback) {
            if (!this.isScanning || !this.detector) return;
            try {
                const barcodes = await this.detector.detect(this.videoEl);
                if (barcodes.length > 0) {
                    const code = barcodes[0].rawValue;
                    console.log('[POSHUB Scanner] Barcode terdeteksi:', code);
                    callback(code);
                }
            } catch (e) {}

            if (this.isScanning) {
                requestAnimationFrame(() => this._scanLoop(callback));
            }
        }

        stop() {
            this.isScanning = false;
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            if (this.videoEl) {
                this.videoEl.srcObject = null;
            }
        }
    }

    // --------------------------------------------------------------------------
    // 3. SUNMI / IMIN POS TERMINAL JS BRIDGE
    // --------------------------------------------------------------------------
    const SunmiBridge = {
        isSunmiTerminal: () => {
            return typeof window.sunmi !== 'undefined' || navigator.userAgent.toLowerCase().includes('sunmi');
        },
        printText: (text) => {
            if (window.sunmi && typeof window.sunmi.printText === 'function') {
                window.sunmi.printText(text);
                window.sunmi.lineWrap(3);
                window.sunmi.cutPaper();
                return true;
            }
            return false;
        }
    };

    // Expose Globally
    window.PoshubBluetoothPrinter = new BluetoothThermalPrinter();
    window.PoshubBarcodeScanner = new CameraBarcodeScanner();
    window.PoshubSunmiBridge = SunmiBridge;
})();
