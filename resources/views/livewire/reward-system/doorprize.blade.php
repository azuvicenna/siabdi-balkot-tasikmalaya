<div>
    {{-- Audio Files --}}
    <input type="hidden" id="src-spin" value="https://assets.mixkit.co/active_storage/sfx/1997/1997-preview.mp3">
    <input type="hidden" id="src-stop" value="https://assets.mixkit.co/active_storage/sfx/2578/2578-preview.mp3">
    <input type="hidden" id="src-win"
        value="https://audio-previews.elements.envatousercontent.com/files/331706982/preview.mp3">

    {{--
    CONTAINER UTAMA
    --}}
    <div id="gameContainer" wire:ignore
        class="bg-white rounded-3xl shadow-2xl w-full max-w-[95vw] mx-auto overflow-hidden flex flex-col min-h-[600px] border-4 border-yellow-500 relative z-10 transition-all duration-500 ease-in-out">

        <div id="fullscreenLogo"
            class="hidden absolute top-6 left-1/2 -translate-x-1/2 z-50 transition-all duration-500 opacity-0 -translate-y-4">

            <img src="/static/images/logo-hknday.png" alt="Logo HKN Day"
                class="h-20 w-auto drop-shadow-2xl filter brightness-100 bg-white/10 p-2 rounded-lg backdrop-blur-sm border border-white/20">
        </div>

        {{-- SLOT MACHINE AREA --}}
        <div
            class="w-full bg-gradient-to-br from-blue-600 to-blue-800 p-8 flex flex-col items-center justify-center relative flex-1">

            {{-- HEADER / JUDUL --}}
            <div class="text-center mb-10 z-10 scale-100 transition-transform duration-500" id="slotHeader">
                <span class="text-blue-200 font-semibold tracking-[0.5em] uppercase text-sm md:text-base">Nomor Kupon
                    Pemenang</span>
                <div class="w-24 h-1 bg-yellow-400 mx-auto mt-2 rounded-full"></div>
            </div>

            {{-- SLOT ANGKA --}}
            <div id="slotWrapper"
                class="flex items-center gap-1 md:gap-3 mb-12 p-4 md:p-8 bg-blue-900/50 rounded-2xl border border-blue-400/30 backdrop-blur-sm shadow-2xl overflow-x-auto justify-center transition-all duration-500">
                <div id="slot-0"
                    class="slot-box w-14 h-20 md:w-24 md:h-36 flex items-center justify-center text-5xl md:text-8xl font-black text-slate-800 rounded-xl bg-white shadow-inner">
                    0</div>
                <div id="slot-1"
                    class="slot-box w-14 h-20 md:w-24 md:h-36 flex items-center justify-center text-5xl md:text-8xl font-black text-slate-800 rounded-xl bg-white shadow-inner">
                    0</div>
                <div id="slot-2"
                    class="slot-box w-14 h-20 md:w-24 md:h-36 flex items-center justify-center text-5xl md:text-8xl font-black text-slate-800 rounded-xl bg-white shadow-inner">
                    0</div>

                <div
                    class="w-4 md:w-10 flex items-center justify-center text-4xl md:text-7xl font-black text-blue-200 pb-2">
                    -</div>

                <div id="slot-3"
                    class="slot-box w-14 h-20 md:w-24 md:h-36 flex items-center justify-center text-5xl md:text-8xl font-black text-slate-800 rounded-xl bg-white shadow-inner border-4 border-yellow-400/50">
                    0</div>
                <div id="slot-4"
                    class="slot-box w-14 h-20 md:w-24 md:h-36 flex items-center justify-center text-5xl md:text-8xl font-black text-slate-800 rounded-xl bg-white shadow-inner border-4 border-yellow-400/50">
                    0</div>
                <div id="slot-5"
                    class="slot-box w-14 h-20 md:w-24 md:h-36 flex items-center justify-center text-5xl md:text-8xl font-black text-slate-800 rounded-xl bg-white shadow-inner border-4 border-yellow-400/50">
                    0</div>
            </div>

            {{-- TOMBOL CONTROLS --}}
            <div class="flex gap-6 z-10 scale-100 transition-transform duration-500" id="slotControls">
                <button id="btnStart" onclick="startSpin()"
                    class="btn-action group relative px-8 py-4 bg-gradient-to-b from-green-400 to-green-600 rounded-full shadow-[0_10px_0_rgb(21,128,61)] active:shadow-[0_2px_0_rgb(21,128,61)] active:translate-y-2 transition-all disabled:from-slate-400 disabled:to-slate-500 disabled:shadow-none disabled:translate-y-2 disabled:cursor-not-allowed">
                    <span
                        class="text-white font-black text-xl md:text-2xl uppercase tracking-wider flex items-center gap-2 cursor-pointer">
                        ⚙️ Mulai Acak
                    </span>
                </button>
                <button id="btnStop" onclick="stopSpinSequence()" disabled
                    class="btn-action px-8 py-4 bg-gradient-to-b from-red-500 to-red-700 rounded-full shadow-[0_10px_0_rgb(153,27,27)] active:shadow-[0_2px_0_rgb(153,27,27)] active:translate-y-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="text-white font-black text-xl md:text-2xl uppercase tracking-wider">✋ STOP!</span>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL PEMENANG --}}
    <div id="winnerModal" class="fixed inset-0 z-[9999] hidden">
        <div class="fixed inset-0 bg-blue-950/90 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border-[6px] border-yellow-400 animate-pop">

                    <div
                        class="bg-gradient-to-r from-blue-900 to-blue-700 px-6 py-6 text-center border-b-4 border-yellow-500">
                        <h3 class="text-3xl font-black text-yellow-300 uppercase tracking-widest drop-shadow-md">🎉
                            SELAMAT! 🎉</h3>
                    </div>

                    <div class="bg-white px-6 pt-8 pb-6 flex flex-col items-center">
                        <div class="relative mb-6">
                            <img id="winnerPhoto" src=""
                                class="relative w-40 h-40 rounded-full border-4 border-white object-cover shadow-xl">
                            <div
                                class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-blue-800 text-yellow-400 text-xs font-bold px-4 py-1 rounded-full uppercase tracking-wider shadow border border-yellow-500 whitespace-nowrap">
                                Winner
                            </div>
                        </div>

                        <div class="text-center w-full mb-4">
                            <h2 id="winnerName" class="text-2xl font-black text-slate-800 leading-tight mb-2 uppercase">
                                Nama Pemenang</h2>
                            <p id="winnerUnit" class="text-lg font-bold text-blue-600 mb-3 uppercase tracking-wide">
                                Unit Kerja
                            </p>
                            <div
                                class="inline-block bg-slate-100 px-4 py-2 rounded-lg border border-slate-300 mb-2 shadow-sm">
                                <p id="winnerCoupon" class="font-mono text-lg text-slate-700 font-bold tracking-widest">
                                    Kupon: 123xxx</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row gap-3 border-t border-slate-200">
                        <button type="button" onclick="confirmWinner()"
                            class="flex-1 inline-flex justify-center items-center rounded-xl bg-gradient-to-b from-green-500 to-green-600 px-4 py-3 text-sm font-bold text-white shadow hover:from-green-600 hover:to-green-700 focus:outline-none transform hover:-translate-y-0.5 transition w-full">
                            <span class="mr-2">✓</span> Tetapkan
                        </button>
                        <button type="button" onclick="cancelWinner()"
                            class="flex-1 inline-flex justify-center items-center rounded-xl bg-white border-2 border-slate-300 px-4 py-3 text-sm font-bold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-red-600 hover:border-red-200 focus:outline-none transition w-full">
                            <span class="mr-2">✕</span> Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // ==========================================
    // 1. UTILITIES (Audio, Confetti, Fullscreen)
    // ==========================================
    
    // Fungsi Toggle Fullscreen Global
    window.toggleFullScreen = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    window.playSoundEffect = function(type) {
        const spinEl = document.getElementById('src-spin');
        const stopEl = document.getElementById('src-stop');
        const winEl = document.getElementById('src-win');
        if (!spinEl || !stopEl || !winEl) return null;

        let audio;
        if (type === 'spin') {
            audio = new Audio(spinEl.value);
            audio.loop = true; audio.volume = 0.6;
        } else if (type === 'stop') {
            audio = new Audio(stopEl.value);
            audio.volume = 1.0;
        } else if (type === 'win') {
            audio = new Audio(winEl.value);
            audio.volume = 1.0;
        }
        if (audio) audio.play().catch(e => console.log("Audio prevented:", e));
        return audio;
    }

    window.fireConfetti = function() {
        var duration = 3000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };
        var colors = ['#d4af37', '#1e3a8a', '#ef4444', '#ffffff'];

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();
            if (timeLeft <= 0) return clearInterval(interval);
            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: 0.1, y: 0.8 }, colors: colors }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: 0.9, y: 0.8 }, colors: colors }));
        }, 250);
    }

    // LISTENER FULLSCREEN YANG DIPERBAIKI + LOGO LOGIC
    document.addEventListener('fullscreenchange', (event) => {
        const container = document.getElementById('gameContainer');
        const body = document.body;
        const navbar = document.getElementById('mainNavbar'); // Pastikan ID navbar di HTML sesuai
        const slotWrapper = document.getElementById('slotWrapper');
        
        // Ambil elemen logo fullscreen yang baru dibuat
        const fsLogo = document.getElementById('fullscreenLogo');

        if (document.fullscreenElement) {
            // === MASUK FULLSCREEN ===
            
            // 1. Bersihkan padding body & Sembunyikan Navbar
            body.classList.remove('p-4', 'pt-24');
            if(navbar) navbar.classList.add('hidden');

            // 2. Modifikasi Container
            if (container) {
                container.classList.remove('max-w-[95vw]', 'mx-auto', 'rounded-3xl', 'border-4', 'shadow-2xl', 'min-h-[600px]', 'relative');
                container.classList.add('fixed', 'inset-0', 'w-screen', 'h-screen', 'rounded-none', 'border-0', 'z-50');
            }

            // 3. Perbesar elemen slot
            if(slotWrapper) {
                slotWrapper.classList.add('scale-110');
            }

            // 4. MUNCULKAN LOGO FULLSCREEN (Baru)
            if(fsLogo) {
                fsLogo.classList.remove('hidden'); // Hapus display:none
                // Beri sedikit delay agar transisi opacity jalan halus
                setTimeout(() => {
                    fsLogo.classList.remove('opacity-0', '-translate-y-4');
                }, 100);
            }

        } else {
            // === KELUAR FULLSCREEN ===

            // 1. Kembalikan padding & Navbar
            body.classList.add('p-4', 'pt-24');
            if(navbar) navbar.classList.remove('hidden');

            // 2. Kembalikan Container
            if (container) {
                container.classList.add('max-w-[95vw]', 'mx-auto', 'rounded-3xl', 'border-4', 'shadow-2xl', 'min-h-[600px]', 'relative');
                container.classList.remove('fixed', 'inset-0', 'w-screen', 'h-screen', 'rounded-none', 'border-0', 'z-50');
            }

            // 3. Reset skala slot
            if(slotWrapper) {
                slotWrapper.classList.remove('scale-110');
            }

            // 4. SEMBUNYIKAN LOGO FULLSCREEN (Baru)
            if(fsLogo) {
                fsLogo.classList.add('opacity-0', '-translate-y-4'); // Fade out dulu
                setTimeout(() => {
                    fsLogo.classList.add('hidden'); // Baru hide total
                }, 500); // Sesuaikan dengan duration CSS
            }
        }
    });

    // ==========================================
    // 2. LOGIKA SLOT (CORE)
    // ==========================================
    
    let intervalId;
    let isSpinning = false;
    let currentWinner = null;
    let spinAudioObject = null;

    window.startSpin = async function() {
        if (isSpinning) return;
        isSpinning = true;

        const btnStart = document.getElementById('btnStart');
        const btnStop = document.getElementById('btnStop');
        const slots = Array.from(document.querySelectorAll('.slot-box'));

        slots.forEach(slot => {
            slot.classList.remove('slot-stopped');
            slot.innerText = "0";
            slot.style.transform = "scale(1)";
        });

        if (spinAudioObject) spinAudioObject.pause();
        spinAudioObject = window.playSoundEffect('spin');

        btnStart.disabled = true;
        btnStart.classList.add('opacity-50', 'cursor-not-allowed');

        intervalId = setInterval(() => {
            slots.forEach(slot => slot.innerText = Math.floor(Math.random() * 10));
        }, 60);

        try {
            const winnerData = await $wire.pickWinner(); 
            if (!winnerData) {
                alert("Data pemenang kosong / habis!");
                location.reload();
                return;
            }
            currentWinner = winnerData;
            btnStop.disabled = false;
            btnStop.classList.remove('opacity-50', 'cursor-not-allowed');

        } catch (error) {
            console.error("Error:", error);
            isSpinning = false;
            btnStart.disabled = false;
            btnStart.classList.remove('opacity-50', 'cursor-not-allowed');
            clearInterval(intervalId);
            if (spinAudioObject) spinAudioObject.pause();
        }
    }

    window.stopSpinSequence = function() {
        if (!isSpinning) return;

        const btnStop = document.getElementById('btnStop');
        btnStop.disabled = true;
        btnStop.classList.add('opacity-50', 'cursor-not-allowed');

        clearInterval(intervalId);

        const rawKupon = String(currentWinner.kode_kupon || '000000');
        const targetString = rawKupon.length > 6 ? rawKupon.slice(-6) : rawKupon.padStart(6, '0');
        const targetNumbers = targetString.split('');
        const slots = Array.from(document.querySelectorAll('.slot-box'));

        slots.forEach((slot, index) => {
            const isSuspenseSlot = index >= 3;
            const spinSpeed = isSuspenseSlot ? 150 : 60; 
            let stopDelay = !isSuspenseSlot ? 500 + (index * 700) : 4000 + ((index - 3) * 1500);

            let localInterval = setInterval(() => {
                slot.innerText = Math.floor(Math.random() * 10);
            }, spinSpeed);

            setTimeout(() => {
                clearInterval(localInterval);
                slot.innerText = targetNumbers[index] !== undefined ? targetNumbers[index] : 0;
                
                window.playSoundEffect('stop');
                slot.classList.add('slot-stopped');
                
                const scaleSize = isSuspenseSlot ? "1.3" : "1.1";
                slot.style.transform = `scale(${scaleSize})`;
                setTimeout(() => slot.style.transform = "scale(1)", 200);

                if (index === slots.length - 1) {
                    isSpinning = false;
                    if (spinAudioObject) spinAudioObject.pause();
                    setTimeout(() => window.showModal(), 1200); 
                }
            }, stopDelay);
        });
    }

    // ==========================================
    // 3. MODAL & ACTIONS
    // ==========================================

    window.showModal = function() {
        // 1. Efek Suara & Confetti
        window.playSoundEffect('win');
        window.fireConfetti();

        // 2. Ambil Elemen Modal & Isiannya
        const modal = document.getElementById('winnerModal');
        const photoEl = document.getElementById('winnerPhoto');
        const nameEl = document.getElementById('winnerName');
        const unitEl = document.getElementById('winnerUnit');
        const couponEl = document.getElementById('winnerCoupon');

        // 3. Isi Data Pemenang (Tanpa Instansi)
        photoEl.src = currentWinner.foto;
        nameEl.innerText = currentWinner.nama;
        unitEl.innerText = currentWinner.unit_kerja || '-';
        couponEl.innerText = "Kupon: " + currentWinner.kode_kupon;

        // 4. Tampilkan Modal
        modal.classList.remove('hidden');
    }

    window.confirmWinner = function () {
        $wire.saveWinner(currentWinner.id); 
        window.closeModal();
    }

    window.cancelWinner = function() {
        $wire.markAsAbsent(currentWinner.id);
        window.closeModal();
    }

    window.closeModal = function() {
        document.getElementById('winnerModal').classList.add('hidden');
        window.resetButtons();
    }

    window.resetButtons = function() {
        const btnStart = document.getElementById('btnStart');
        const btnStop = document.getElementById('btnStop');
        
        btnStart.disabled = false;
        btnStart.classList.remove('opacity-50', 'cursor-not-allowed');
        btnStop.disabled = true;
        btnStop.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // ==========================================
    // 4. KEYBOARD LISTENER (SHORTCUTS)
    // ==========================================
    document.addEventListener('keydown', function(event) {
        const btnStart = document.getElementById('btnStart');
        const btnStop = document.getElementById('btnStop');
        const modal = document.getElementById('winnerModal');

        // Pastikan modal sedang tertutup (biar gak kepencet saat modal muncul)
        const isModalClosed = modal.classList.contains('hidden');

        // --- AKSI START (TOMBOL SHIFT) ---
        if (event.key === 'Shift') {
            // Syarat: Tidak sedang muter, Tombol Start Aktif, Modal Tutup
            if (!isSpinning && !btnStart.disabled && isModalClosed) {
                event.preventDefault(); // Mencegah aksi default browser
                startSpin();            // JALANKAN SPIN
            }
        }

        // --- AKSI STOP (TOMBOL ENTER) ---
        if (event.key === 'Enter') {
            // Syarat: Sedang muter, Tombol Stop Aktif (Data Ready), Modal Tutup
            if (isSpinning && !btnStop.disabled && isModalClosed) {
                event.preventDefault();
                stopSpinSequence();     // HENTIKAN SPIN
            }
        }
    });
</script>
@endscript
</div>