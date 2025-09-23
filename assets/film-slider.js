const container = document.querySelector('.film-slider-container');
const slider = document.querySelector('.film-slider');
if (container && slider) {

  slider.innerHTML += slider.innerHTML;

  let scrollAmount = 0;
  const slideWidth = slider.scrollWidth / 2;

  function autoScroll() {
    scrollAmount += 2;
    if (scrollAmount >= slideWidth) {
      scrollAmount = 0;
    }
    container.scrollLeft = scrollAmount;
    requestAnimationFrame(autoScroll);
  }
  autoScroll();
}