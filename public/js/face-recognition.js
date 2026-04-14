const FaceRecognition = {
    isLoaded: false,

    async loadModels() {
        if (this.isLoaded) return true;
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri("/models"),
                faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
            ]);
            this.isLoaded = true;
            return true;
        } catch (err) {
            console.error("Gagal memuat model Face API:", err);
            return false;
        }
    },

    async detectFace(canvas) {
        const options = new faceapi.TinyFaceDetectorOptions({
            scoreThreshold: 0.6,
        });
        return await faceapi
            .detectSingleFace(canvas, options)
            .withFaceLandmarks();
    },

    validate(detection) {
        if (!detection) {
            return {
                valid: false,
                type: "not_detected",
                message:
                    "Wajah tidak terdeteksi! Pastikan wajah terlihat jelas dan pencahayaan cukup.",
            };
        }
        const faceWidth = detection.detection.box.width;
        if (faceWidth < 150) {
            return {
                valid: false,
                type: "too_far",
                message:
                    "Wajah terlalu jauh dari kamera. Tolong mendekat dan pas-kan wajah di frame!",
            };
        }
        return {
            valid: true,
            type: "success",
            message: "Wajah berhasil terdeteksi.",
        };
    },
};
