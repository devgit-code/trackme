document.addEventListener('livewire:navigated', () => {
    [...document.getElementsByClassName('qr-scanner')].forEach(async el => {
        const QrScanner = (await import('qr-scanner')).default;
        function processQR(result) {
            // const errorModal = new bootstrap.Modal(document.getElementById('qrErrorModal'));
            if (result.data && result.data.startsWith("https://trackme.info")) {
                qrScanner.destroy();
                window.location.href = result.data;
            } else {
                qrScanner.stop();
                if (result.data) {
                    // document.getElementById("qr-data").innerText = "Found:\n" + result.data;
                } else {
                    // document.getElementById("qr-data").innerText = "No QR Found";
                }
                // errorModal.show();
            }
        }
        var qrScanner = new QrScanner(
            el,
            result => processQR(result), {
            highlightScanRegion: true,
            preferredCamera: 'environment',
            highlightCodeOutline: true,
            // overlay: document.getElementById("qroverlay")
        });
        QrScanner.listCameras(true).then(function (cameras) {
            cameras = cameras.filter(function (camera) {
                return camera.id !== "";
            });
            if (cameras.length > 0) {
                // document.getElementById("camera-alert").classList.add("d-none");
                qrScanner.start();
            }
        }).catch(function (error) {
            // document.getElementById("camera-alert").classList.remove("d-none");
            // document.getElementById("camera-init-msg").classList.add("d-none");
        });
        document.addEventListener('livewire:navigating', () => {
            qrScanner.destroy();
        })
        // document.getElementById("qr-file").addEventListener('change', event => {
        //     const file = fileSelector.files[0];
        //     if (!file) {
        //         return;
        //     }
        //     QrScanner.scanImage(file, {
        //             returnDetailedScanResult: true,
        //             alsoTryWithoutScanRegion: true
        //         })
        //         .then(result => processQR(result))
        //         .catch(e => processQR(e));
        // });
    });
});
// var scannerElement = document.getElementById("qrscanner");
// scannerElement.addEventListener('playing', function() {
//     document.getElementById("camera-init-msg").classList.add("d-none");
// });
// scannerElement.addEventListener('pause', function() {
//     document.getElementById("camera-init-msg").classList.remove("d-none");
// });
