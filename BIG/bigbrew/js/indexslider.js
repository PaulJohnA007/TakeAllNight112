document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll(".slider .slides img");
    let currentSlide = 0;
  
    function showSlide(index) {
      slides.forEach((slide, i) => {
        if (i === index) {
          slide.style.display = "block";
        } else {
          slide.style.display = "none";
        }
      });
    }
  
    function nextSlide() {
      currentSlide = (currentSlide + 1) % slides.length;
      showSlide(currentSlide);
    }
  
    // Show the initial slide
    showSlide(currentSlide);
  
    // Auto slide
    setInterval(nextSlide, 5000); // Change 2000 to adjust the interval (in milliseconds)
  });