<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/output.css') }} " rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
</head>

<body class="font-poppins text-[#0A090B] px-5">
    <section id="content" class="flex w-full">

        <!-- Tambahkan overlay setelah sidebar -->
        <div id="overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40"></div>

        <!-- CONTENT  -->
        <div id="menu-content" class="flex flex-col w-full md:w-full lg:w-full pb-[30px]">
            <div class="nav flex justify-between p-5 border-b border-[#EEEEEE]">

                <div class="flex items-center gap-[30px]">
                    <div class="flex gap-3 items-center">
                        <a href="index.html" class="flex items-center justify-center">
                            <img class="w-[100px]" src="{{ asset('images/logo/Logo-Mec.png') }}" alt="logo">
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-[30px]">
                    <div class="flex gap-3 items-center">
                        <div class="flex flex-col text-end">
                            {{-- <p class="text-sm text-[#7F8190]">Waktu</p>
                            <p class="font-semibold">99:00</p> --}}
                        </div>
                    </div>
                </div>

                <!-- Menu Hamburger -->
                <button id="hamburger" class="md:hidden block p-4 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

            </div>

            <form method="POST"
                action="{{ route('dashboard.learning.course.answer.store', ['course' => $course->id, 'question' => $question->id]) }}"
                class="learning flex flex-col gap-8 items-center mt-7 w-full pb-8 px-5 md:px-0">
                @csrf
                <div id="image-preview-question"
                    class="{{ $question->type == 'image' ? '' : 'hidden' }} relative w-[200px] overflow-hidden peer-data-[empty=true]:border-[3px] peer-data-[empty=true]:border-dashed peer-data-[empty=true]:border-[#EEEEEE]">
                    <div
                        class="relative file-preview z-10 w-full h-full {{ $question->type == 'image' ? '' : 'hidden' }}">
                        @if ($question->type == 'image' && $question->question)
                            <img src="{{ asset('storage/' . $question->question) }}"
                                class="thumbnail-icon w-full h-full object-cover" alt="thumbnail">
                        @endif
                    </div>
                </div>
                <h1
                    class="{{ $question->type == 'text' ? '' : 'hidden' }} w-[100%] md:w-[90%] my-0 font-extrabold text-[16px] leading-[30px] md:leading-[30px] text-center">
                    [ {{ $question->number }}. ] {{ $question->question }}
                </h1>
                <div class="flex flex-col gap-3 md:gap-4 w-full max-w-[100%] md:w-[90%] mb-5">
                    @foreach ($question->answers as $i => $answer)
                        <label for="answer-{{ $i }}"
                            class="group flex items-center justify-between rounded-full w-full border border-[#EEEEEE] p-3 transition-all duration-300 has-[:checked]:border-2 has-[:checked]:border-[#0A090B]">

                            <!-- Text or Image Answer Preview -->
                            <div class="flex items-center gap-3">
                                <!-- Jika teks -->
                                <img src="{{ asset('/images/icons/arrow-circle-right.svg') }}" alt="icon">

                                @if ($answer->type == 'text')
                                    <span class="font-semibold text-base md:text-md">{{ $answer->answer ?? '' }}</span>
                                @endif

                                <!-- Jika gambar -->
                                @if ($answer->type == 'image' && $answer->answer)
                                    <img class="w-[200px]" src="{{ asset('storage/' . $answer->answer) }}"
                                        class="thumbnail-icon w-[50px] h-[50px] object-cover" alt="image-answer">
                                @endif
                            </div>

                            <div class="hidden group-has-[:checked]:block">
                                <img src="{{ asset('/images/icons/tick-circle.svg') }}" alt="tick-icon">
                            </div>

                            <!-- Radio Button -->
                            <input type="radio" name="answer_id" id="answer-{{ $i }}"
                                value="{{ $answer->id }}" class="hidden">
                        </label>
                    @endforeach
                </div>
                <div class="flex flex-nowrap gap-4">
                    <!-- Button Kembali (previous) -->
                    <button type="button" id="previousBtn"
                        class="w-fit p-[14px_40px] bg-[#e05d28] rounded-full font-bold text-sm text-white transition-all duration-300 hover:shadow-[0_4px_15px_0_#6436F14D] text-center">
                        Kembali
                    </button>

                    <!-- Button Lanjut (submit) -->
                    <button type="submit"
                        class="w-fit p-[14px_40px] bg-[#6436F1] rounded-full font-bold text-sm text-white transition-all duration-300 hover:shadow-[0_4px_15px_0_#6436F14D] text-center">
                        Lanjut
                    </button>
                </div>
            </form>

        </div>

        <div id="sidebar"
            class="w-[80%] md:w-[370px] flex flex-col shrink-0 min-h-screen justify-between p-[30px] border-r border-[#EEEEEE] bg-[#FBFBFB]">
            <div class="w-full flex flex-col gap-[30px]">
                <div class="flex flex-col text-center">
                    <p class="font-semibold">Soal</p>
                    <p class="mt-2 p-2 bg-red-200 text-gray-600 rounded-lg text-sm">Klik <b class="text-bold">Lanjut</b>
                        Untuk Menyimpan Jawaban</p>
                </div>

                <div class="grid grid-cols-5 md:grid-cols-6 gap-4 max-h-[750px] md:max-h-[600px] overflow-y-auto">
                    @foreach ($questions as $question)
                        <a href="{{ route('dashboard.learning.course', ['course' => $course->id, 'question' => $question->id]) }}"
                            class="bg-gray-200 rounded-md text-center py-2">
                            {{ $question->number }}
                        </a>
                    @endforeach
                </div>

            </div>
        </div>
    </section>

    <script>
        document.getElementById('hamburger').addEventListener('click', function(e) {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');

            sidebar.classList.toggle('active');

            // Tampilkan atau sembunyikan overlay dengan animasi
            if (overlay.classList.contains('show')) {
                overlay.classList.remove('show');
                setTimeout(function() {
                    overlay.style.display = 'none'; // Sembunyikan setelah animasi selesai
                }, 300); // Waktu sesuai dengan durasi transition (0.3s)
            } else {
                overlay.style.display = 'block'; // Tampilkan segera
                setTimeout(function() {
                    overlay.classList.add('show'); // Mulai animasi fade-in
                }, 0); // Penundaan kecil untuk memulai transisi
            }

            // Cegah event bubbling ke document saat hamburger diklik
            e.stopPropagation();
        });

        // Deteksi klik di luar sidebar dan tutup sidebar serta overlay
        document.addEventListener('click', function(e) {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');
            var isClickInsideSidebar = sidebar.contains(e.target);
            var isHamburgerClicked = document.getElementById('hamburger').contains(e.target);

            // Jika sidebar aktif dan klik terjadi di luar sidebar dan bukan hamburger
            if (sidebar.classList.contains('active') && !isClickInsideSidebar && !isHamburgerClicked) {
                sidebar.classList.remove('active');
                overlay.classList.remove('show');
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 300);
            }
        });

        // Tutup sidebar dan overlay jika overlay di klik
        document.getElementById('overlay').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');

            sidebar.classList.remove('active');
            overlay.classList.remove('show');
            setTimeout(function() {
                overlay.style.display = 'none';
            }, 0);
        });
    </script>



</body>

</html>
