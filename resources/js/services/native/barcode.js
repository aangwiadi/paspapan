import { BarcodeScanner } from "@capacitor-community/barcode-scanner";

let isScanning = false;
let currentFacingMode = 'environment';

export async function startNativeBarcodeScanner(onScanSuccess, facingMode = null) {
    if (isScanning) return;
    isScanning = true;
    
    if (facingMode) currentFacingMode = facingMode;

    const perm = await BarcodeScanner.checkPermission({ force: true });

    if (perm.denied) {
        alert("Camera permission denied. Please enable in settings.");
        isScanning = false;
        return;
    }

    if (!perm.granted) {
        alert("Camera permission is required");
        isScanning = false;
        return;
    }

    document.body.classList.add('is-native-scanning');
    document.documentElement.classList.add('is-native-scanning');
    
    await BarcodeScanner.hideBackground();
    
    if (window.setShowOverlay) window.setShowOverlay(true);

    try {
        
        const result = await BarcodeScanner.startScan({ 
             cameraDirection: currentFacingMode === 'user' ? 1 : 0 
        });

        if (result?.hasContent) {
            await onScanSuccess(result.content);
        }
    } catch (e) {
        console.error("Scanner failed", e);
        alert("Scanner Error: " + (e.message || e));
    } finally {
        if (window.setShowOverlay) window.setShowOverlay(false);
        
        BarcodeScanner.showBackground();
        document.body.classList.remove('is-native-scanning');
        document.documentElement.classList.remove('is-native-scanning');
        
        try { await BarcodeScanner.stopScan(); } catch(e){}
        isScanning = false;
    }
}

export async function stopNativeBarcodeScanner() {
    BarcodeScanner.showBackground();
    document.body.classList.remove('is-native-scanning');
    document.documentElement.classList.remove('is-native-scanning');
    try { await BarcodeScanner.stopScan(); } catch(e){}
    isScanning = false;
}

export async function switchNativeCamera(onScanSuccess) {
    await stopNativeBarcodeScanner();
     
    currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
    
    setTimeout(() => {
        startNativeBarcodeScanner(onScanSuccess, currentFacingMode);
    }, 300);
}
