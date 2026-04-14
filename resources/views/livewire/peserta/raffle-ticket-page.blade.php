<div>
    <div class="w-full max-w-[420px]">
        <div class="bg-[#DDE2FF] rounded-3xl  mb-6 relative mt-12">
            <div class="absolute -top-16 left-1/2 -translate-x-1/2">
                <img src="{{ $detailData['fotoSelfie'] }}" alt="Selfie {{ $detailData['nama'] ?? 'Peserta' }}"
                    class="w-32 h-32 rounded-full object-cover border-4 border-[#DDE2FF] bg-white shadow-md">
            </div>


            <div class="p-4 pt-20 space-y-3">

                {{-- Status Badge --}}
                @if ($statusCoupon == 'Aktif')
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-[14px] font-semibold bg-green-700 text-white">
                        {{ $statusCoupon }}
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-[14px] font-semibold bg-red-700 text-white">
                        {{ $statusCoupon }}
                    </span>
                @endif

                {{-- Nomor Kupon --}}
                <div class="space-y-1">
                    <p class="text-[12px] text-gray-500 font-medium tracking-wide uppercase">
                        Nomor Kupon Undian Kamu
                    </p>
                    <p class="text-3xl font-extrabold text-[#1E5BD8] leading-tight">
                        {{ $couponNumber }}
                    </p>
                </div>

                {{-- Detail Peserta --}}
                <div class="mt-2 space-y-1 text-[14px]">
                    <div class="flex justify-between gap-4">
                        <div>
                            <p class="text-gray-500">Nama peserta</p>
                            <p class="text-gray-800 font-semibold">
                                {{ $detailData['nama'] ?? 'tidak ada' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Asal Instansi</p>
                            <p class="text-gray-800 font-semibold">
                                {{ $detailData['unit_kerja'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative my-6">
                    <div class="absolute -left-6 top-1/2 -translate-y-1/2 w-7 h-7 bg-white rounded-full"></div>
                    <div class="absolute -right-6 top-1/2 -translate-y-1/2 w-7 h-7 bg-white rounded-full"></div>
                    <div class="border-t-2 border-dashed border-white"></div>
                </div>


                <button type="button" wire:click="downloadCoupon" wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor wait"
                    class="w-full bg-[#5065A4] text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none flex items-center justify-center">
                    <span wire:loading.remove wire:target="downloadCoupon">Download Doorprize</span>

                    <span wire:loading wire:target="downloadCoupon" class="flex items-center gap-1">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        {{-- Logo Footer --}}
        <div class="mt-4 flex justify-center gap-6 items-center">
            <x-peserta.logo />
        </div>

    </div>

    <script>
        // Slider animation banner Doorprize
        document.addEventListener('DOMContentLoaded', function() {

            const slides = document.getElementsByClassName('banner-slide');
            const dots = document.getElementById('bannerDots').children;

            if (!slides.length) return;

            let index = 0;
            const total = slides.length;

            function showSlide(i) {
                index = i % total;

                // Update slide visibility
                for (let s = 0; s < total; s++) {
                    slides[s].style.opacity = (s === index) ? '1' : '0';
                }

                // Update dots
                for (let d = 0; d < total; d++) {
                    dots[d].style.backgroundColor = (d === index) ? '#6b7280' : '#d1d5db';
                }
            }

            setInterval(() => {
                showSlide(index + 1);
            }, 3000);
        });
    </script>
</div>
