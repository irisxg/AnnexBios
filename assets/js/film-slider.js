const container = document.querySelector(".film-slider-container");
const slider = document.querySelector(".film-slider");

slider.innerHTML += slider.innerHTML;

const slideWidth = slider.scrollWidth / 2;
let scrollAmount = 20;
let hovering = false;

container.addEventListener("mouseenter", () => {
  console.log("hover");
  hovering = true;
});

container.addEventListener("mouseleave", () => {
  console.log("hover");
  hovering = false;
});

function autoScroll() {
  //console.log(container);

  //  if (scrollAmount >= slideWidth) {
  //     scrollAmount = 0;
  //   }
  if (!hovering) {
    container.scrollLeft += scrollAmount;
  }
  // console.log(scrollAmount, slideWidth);
  requestAnimationFrame(autoScroll);
}
autoScroll();
