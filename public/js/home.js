const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");
const menuBtnIcon = menuBtn.querySelector("i");
const logo = document.getElementById("logo");

menuBtn.addEventListener("click", (e) => {
    navLinks.classList.toggle("open");

    const isOpen = navLinks.classList.contains("open");
    menuBtnIcon.setAttribute("class", isOpen ? "ri-close-line" : "ri-menu-line");

    // đổi logo
    logo.setAttribute("src", isOpen ? "/Logo-1.webp" : "/Logo.png");
});

navLinks.addEventListener("click", (e) => {
    navLinks.classList.remove("open");
    menuBtnIcon.setAttribute("class", "ri-menu-line");
    logo.setAttribute("src", "/Logo.png");
});
const scrollRevealOption = {
    distance: "50px",
    origin: "bottom",
    duration: 1000,
};

function applyScrollReveal() {
    const scrollRevealOption = {
        distance: "50px",
        origin: "bottom",
        duration: 1000,
    };

    ScrollReveal().reveal(".header__container h2", { ...scrollRevealOption });
    ScrollReveal().reveal(".header__container p", { ...scrollRevealOption, delay: 500 });
}

document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.header__slide');
    let currentIndex = 0;
    let slideInterval;

    const showSlide = (index) => {
        slides.forEach((slide, i) => {
            slide.classList.remove('active', 'previous');
            if (i === index) {
                slide.classList.add('active');
            } else if (i === currentIndex) {
                slide.classList.add('previous');
            }
        });

        // Xoá class 'previous' sau một khoảng thời gian
        setTimeout(() => {
            slides.forEach(slide => slide.classList.remove('previous'));
        }, 1200);
    };

    const showNextSlide = () => {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    };

    // Khởi động slide đầu tiên
    showSlide(currentIndex);
    slideInterval = setInterval(showNextSlide, 5000);

    // Gọi ScrollReveal nếu có
    if (typeof applyScrollReveal === 'function') {
        applyScrollReveal();
    }
});


ScrollReveal().reveal(".about__image img", {
    ...scrollRevealOption,
    origin: "left",
});
ScrollReveal().reveal(".about__content .section__subheader", {
    ...scrollRevealOption,
    delay: 500,
});

ScrollReveal().reveal(".about__content .section__header", {
    ...scrollRevealOption,
    delay: 1000,
});

ScrollReveal().reveal(".about__content .section__description", {
    ...scrollRevealOption,
    delay: 1500,
});

ScrollReveal().reveal(".offers__container .section__subheader", {
    ...scrollRevealOption,
    delay: 300,
});

ScrollReveal().reveal(".offers__container .section__header", {
    ...scrollRevealOption,
    delay: 600,
});

ScrollReveal().reveal(".offers__container .section__description", {
    ...scrollRevealOption,
    delay: 900,
});

ScrollReveal().reveal(".offers__card", {
    ...scrollRevealOption,
    interval: 200,
    origin: "bottom",
});
