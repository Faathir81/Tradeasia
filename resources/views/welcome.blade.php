<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Tradeasia') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Our Top Products CSS -->
        <link href="{{ asset('css/top-products.css') }}" rel="stylesheet">

        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: 'Inter', sans-serif;
                background: #f5f8fc;
            }
        </style>
    </head>
    <body>

        <!-- ===== OUR TOP PRODUCTS SECTION ===== -->
        <section class="top-products-section">
            <div class="container">
                <!-- Header -->
                <div class="top-products-header">
                    <h2 class="top-products-title">Our Top Products</h2>
                    <div class="carousel-arrow-btns">
                        <button class="carousel-arrow-btn" id="topProductsPrev" aria-label="Previous">&#8249;</button>
                        <button class="carousel-arrow-btn" id="topProductsNext" aria-label="Next">&#8250;</button>
                    </div>
                </div>

                <!-- Carousel Wrapper -->
                <div class="top-products-carousel-wrapper" id="topProductsWrapper">
                    <div class="top-products-carousel-track" id="topProductsTrack">
                        @foreach ($products as $product)
                            @php
                                $lang = $product->englishLang;
                                $imgUrl = $product->productimage
                                    ? 'https://cdn.chemtradeasia.com' . $product->productimage
                                    : null;
                            @endphp
                            @if ($lang)
                                <div class="product-card-col">
                                    <div class="product-card">
                                        <div class="product-card-img-wrapper">
                                            @if ($imgUrl)
                                                <img
                                                    src="{{ $imgUrl }}"
                                                    alt="{{ $lang->productname }}"
                                                    loading="lazy"
                                                    onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'"
                                                >
                                            @else
                                                <img src="https://via.placeholder.com/300x300?text=No+Image" alt="No Image">
                                            @endif
                                        </div>
                                        <div class="product-card-name">{{ $lang->productname }}</div>
                                        <div class="product-card-meta">CAS Number : <span>{{ $lang->cas_number ?? '-' }}</span></div>
                                        <div class="product-card-meta">HS Code : <span>{{ $lang->hs_code ?? '-' }}</span></div>
                                        <a href="#" class="product-card-btn">Inquire Now</a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Dots Indicator -->
                <div class="carousel-dots" id="topProductsDots"></div>
            </div>
        </section>
        <!-- ===== END OUR TOP PRODUCTS SECTION ===== -->

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Carousel Script -->
        <script>
        (function () {
            const track = document.getElementById('topProductsTrack');
            const dotsContainer = document.getElementById('topProductsDots');
            const prevBtn = document.getElementById('topProductsPrev');
            const nextBtn = document.getElementById('topProductsNext');

            let currentIndex = 0;

            function getItemsPerSlide() {
                const w = window.innerWidth;
                if (w >= 992) return 4;
                if (w >= 576) return 2;
                return 1;
            }

            function getTotalCards() {
                return track.querySelectorAll('.product-card-col').length;
            }

            function getTotalSlides() {
                return Math.ceil(getTotalCards() / getItemsPerSlide());
            }

            function buildDots() {
                dotsContainer.innerHTML = '';
                const total = getTotalSlides();
                for (let i = 0; i < total; i++) {
                    const dot = document.createElement('button');
                    dot.classList.add('carousel-dot');
                    if (i === currentIndex) dot.classList.add('active');
                    dot.addEventListener('click', () => goTo(i));
                    dotsContainer.appendChild(dot);
                }
            }

            function updateDots() {
                dotsContainer.querySelectorAll('.carousel-dot').forEach((dot, i) => {
                    dot.classList.toggle('active', i === currentIndex);
                });
            }

            function goTo(index) {
                const total = getTotalSlides();
                currentIndex = (index + total) % total;
                const items = getItemsPerSlide();
                const percent = (currentIndex * items * 100) / getTotalCards();
                track.style.transform = `translateX(-${percent}%)`;
                updateDots();
            }

            prevBtn.addEventListener('click', () => goTo(currentIndex - 1));
            nextBtn.addEventListener('click', () => goTo(currentIndex + 1));

            window.addEventListener('resize', () => {
                currentIndex = 0;
                buildDots();
                track.style.transform = 'translateX(0)';
            });

            buildDots();
        })();
        </script>
    </body>
</html>
