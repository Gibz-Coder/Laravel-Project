(function () {
  "use strict";

  /* page loader */
  function hideLoader() {
    const loader = document.getElementById("loader");
    loader.classList.add("d-none")
  }

  window.addEventListener("load", hideLoader);
  /* page loader */

  /* tooltip */
  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  /* popover  */
  const popoverTriggerList = document.querySelectorAll(
    '[data-bs-toggle="popover"]'
  );
  const popoverList = [...popoverTriggerList].map(
    (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
  );

  /* breadcrumb date range picker */
  flatpickr("#daterange", {
    mode: "range",
    dateFormat: "F, d Y",
    defaultDate: ["May, 01 2024", "May, 30 2024"],
    disableMobile: true
  });
  /* breadcrumb date range picker */

  /* header theme toggle */
  function toggleTheme() {
    let html = document.querySelector("html");
    if (html.getAttribute("data-theme-mode") === "dark") {
      html.setAttribute("data-theme-mode", "light");
      html.setAttribute("data-header-styles", "light");
      html.setAttribute("data-menu-styles", "light");
      if (!localStorage.getItem("primaryRGB")) {
        html.setAttribute("style", "");
      }
      html.removeAttribute("data-bg-theme");
      if (document.querySelector("#switcher-canvas")) {
        document.querySelector("#switcher-light-theme").checked = true;
        document.querySelector("#switcher-menu-light").checked = true;
      }
      document.querySelector("html").style.removeProperty("--body-bg-rgb", localStorage.bodyBgRGB);
      html.style.removeProperty("--body-bg-rgb2");
      html.style.removeProperty("--light-rgb");
      html.style.removeProperty("--form-control-bg");
      html.style.removeProperty("--input-border");
      if (document.querySelector("#switcher-canvas")) {
        document.querySelector("#switcher-header-light").checked = true;
        document.querySelector("#switcher-menu-light").checked = true;
        document.querySelector("#switcher-light-theme").checked = true;
        document.querySelector("#switcher-background4").checked = false;
        document.querySelector("#switcher-background3").checked = false;
        document.querySelector("#switcher-background2").checked = false;
        document.querySelector("#switcher-background1").checked = false;
        document.querySelector("#switcher-background").checked = false;
      }
      localStorage.removeItem("zynixdarktheme");
      localStorage.removeItem("zynixMenu");
      localStorage.removeItem("zynixHeader");
      localStorage.removeItem("bodylightRGB");
      localStorage.removeItem("bodyBgRGB");
      html.setAttribute("data-header-styles", "light");
    } else {
      html.setAttribute("data-theme-mode", "dark");
      html.setAttribute("data-header-styles", "dark");
      if (!localStorage.getItem("primaryRGB")) {
        html.setAttribute("style", "");
      }
      html.setAttribute("data-menu-styles", "dark");
      if (document.querySelector("#switcher-canvas")) {
        document.querySelector("#switcher-dark-theme").checked = true;
        document.querySelector("#switcher-menu-dark").checked = true;
        document.querySelector("#switcher-header-dark").checked = true;
        document.querySelector("#switcher-menu-dark").checked = true;
        document.querySelector("#switcher-header-dark").checked = true;
        document.querySelector("#switcher-dark-theme").checked = true;
        document.querySelector("#switcher-background4").checked = false;
        document.querySelector("#switcher-background3").checked = false;
        document.querySelector("#switcher-background2").checked = false;
        document.querySelector("#switcher-background1").checked = false;
        document.querySelector("#switcher-background").checked = false;
      }
      localStorage.setItem("zynixdarktheme", "true");
      localStorage.setItem("zynixMenu", "dark");
      localStorage.setItem("zynixHeader", "dark");
      localStorage.removeItem("bodylightRGB");
      localStorage.removeItem("bodyBgRGB");
    }
  }
  let layoutSetting = document.querySelector(".layout-setting");
  layoutSetting.addEventListener("click", toggleTheme);
  /* header theme toggle */

  /* Choices JS */
  document.addEventListener("DOMContentLoaded", function () {
    var genericExamples = document.querySelectorAll("[data-trigger]");
    for (let i = 0; i < genericExamples.length; ++i) {
      var element = genericExamples[i];
      new Choices(element, {
        allowHTML: true,
        placeholderValue: "This is a placeholder set in the config",
        searchPlaceholderValue: "Search",
      });
    }
  });
  /* Choices JS */

  /* footer year */
  document.getElementById("year").innerHTML = new Date().getFullYear();
  /* footer year */

  /* node waves */
  Waves.attach(".btn-wave", ["waves-light"]);
  Waves.init();
  /* node waves */

  /* card with close button */
  let DIV_CARD = ".card";
  let cardRemoveBtn = document.querySelectorAll(
    '[data-bs-toggle="card-remove"]'
  );
  cardRemoveBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      e.preventDefault();
      let $this = this;
      let card = $this.closest(DIV_CARD);
      card.remove();
      return false;
    });
  });
  /* card with close button */

  /* card with fullscreen */
  let cardFullscreenBtn = document.querySelectorAll(
    '[data-bs-toggle="card-fullscreen"]'
  );
  cardFullscreenBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      let $this = this;
      let card = $this.closest(DIV_CARD);
      card.classList.toggle("card-fullscreen");
      card.classList.remove("card-collapsed");
      e.preventDefault();
      return false;
    });
  });
  /* card with fullscreen */

  /* count-up */
  var i = 1;
  setInterval(() => {
    document.querySelectorAll(".count-up").forEach((ele) => {
      if (ele.getAttribute("data-count") >= i) {
        i = i + 1;
        ele.innerText = i;
      }
    });
  }, 10);
  /* count-up */

  /* back to top */
  const scrollToTop = document.querySelector(".scrollToTop");
  const $rootElement = document.documentElement;
  const $body = document.body;
  window.onscroll = () => {
    const scrollTop = window.scrollY || window.pageYOffset;
    const clientHt = $rootElement.scrollHeight - $rootElement.clientHeight;
    if (window.scrollY > 100) {
      scrollToTop.style.display = "flex";
    } else {
      scrollToTop.style.display = "none";
    }
  };
  scrollToTop.onclick = () => {
    window.scrollTo(0, 0);
  };
  /* back to top */
  
})();

/* full screen */
document.addEventListener('DOMContentLoaded', function() {
  // Get the fullscreen toggle button
  const fullscreenToggle = document.getElementById('fullscreen-toggle');
  if (fullscreenToggle) {
    fullscreenToggle.addEventListener('click', toggleFullscreen);
  }

  // Add fullscreen change event listeners for different browsers
  document.addEventListener('fullscreenchange', updateFullscreenIcons);
  document.addEventListener('webkitfullscreenchange', updateFullscreenIcons);
  document.addEventListener('mozfullscreenchange', updateFullscreenIcons);
  document.addEventListener('MSFullscreenChange', updateFullscreenIcons);
});

// For backward compatibility
window.openFullscreen = function() {
  toggleFullscreen();
};

function toggleFullscreen() {
  try {
    const doc = document.documentElement;

    if (!isFullscreen()) {
      // Enter fullscreen
      if (doc.requestFullscreen) {
        doc.requestFullscreen();
      } else if (doc.webkitRequestFullscreen) { // Safari
        doc.webkitRequestFullscreen();
      } else if (doc.mozRequestFullScreen) { // Firefox
        doc.mozRequestFullScreen();
      } else if (doc.msRequestFullscreen) { // IE/Edge
        doc.msRequestFullscreen();
      }
    } else {
      // Exit fullscreen
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) { // Safari
        document.webkitExitFullscreen();
      } else if (document.mozCancelFullScreen) { // Firefox
        document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) { // IE/Edge
        document.msExitFullscreen();
      }
    }
  } catch (error) {
    console.error('Error toggling fullscreen:', error);
  }
}

function isFullscreen() {
  return !!(document.fullscreenElement ||
           document.webkitFullscreenElement ||
           document.mozFullScreenElement ||
           document.msFullscreenElement);
}

function updateFullscreenIcons() {
  try {
    const openIcon = document.querySelector('.fullscreen-open');
    const closeIcon = document.querySelector('.fullscreen-close');

    if (!openIcon || !closeIcon) return;

    if (isFullscreen()) {
      // Show exit fullscreen icon
      openIcon.classList.add('d-none');
      closeIcon.classList.remove('d-none');
    } else {
      // Show enter fullscreen icon
      openIcon.classList.remove('d-none');
      closeIcon.classList.add('d-none');
    }
  } catch (error) {
    console.error('Error updating fullscreen icons:', error);
  }
}
/* full screen */

/* toggle switches */
let customSwitch = document.querySelectorAll(".toggle");
customSwitch.forEach((e) =>
  e.addEventListener("click", () => {
    e.classList.toggle("on");
  })
);
/* toggle switches */

/* header dropdown close button */

/* for cart dropdown */
const headerbtn = document.querySelectorAll(".dropdown-item-close");

headerbtn.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();

    // Find the closest parent element with class 'dropdown-item'
    const listItem = button.closest('.dropdown-item');
    if (listItem) {
      listItem.remove(); // Remove the list item
    }

    // Update the cart badge and cart data
    const itemCount = document.querySelectorAll(".dropdown-item-close").length;
    document.getElementById("cart-data").innerText = `${itemCount} Items`;
    document.getElementById("cart-icon-badge").innerText = `${itemCount}`;

    // Check if there are no items left
    if (itemCount === 0) {
      document.querySelector(".empty-header-item").classList.add("d-none");
      document.querySelector(".empty-item").classList.remove("d-none");
    }
  });
});

/* for cart dropdown */

/* for notifications dropdown */
const headerbtn1 = document.querySelectorAll(".dropdown-item-close1");
headerbtn1.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    button.parentNode.parentNode.parentNode.parentNode.remove();
    document.getElementById("notifiation-data").innerText = `${document.querySelectorAll(".dropdown-item-close1").length
      } Unread`;
    if (document.querySelectorAll(".dropdown-item-close1").length == 0) {
      let elementHide1 = document.querySelector(".empty-header-item1");
      let elementShow1 = document.querySelector(".empty-item1");
      elementHide1.classList.add("d-none");
      elementShow1.classList.remove("d-none");
    }
  });
});

/* for notifications dropdown */


// for nummber of products selected
var value = 1,
minValue = 0,
maxValue = 30;

let productMinusBtn = document.querySelectorAll(".product-quantity-minus")
let productPlusBtn = document.querySelectorAll(".product-quantity-plus")
productMinusBtn.forEach((element) => {
element.onclick = () => {
    value = Number(element.parentElement.childNodes[3].value)
    if (value > minValue) {
        value = Number(element.parentElement.childNodes[3].value) - 1;
        element.parentElement.childNodes[3].value = value;
    }
}
})
productPlusBtn.forEach((element) => {
element.onclick = () => {
    if (value < maxValue) {
        value = Number(element.parentElement.childNodes[3].value) + 1;
        element.parentElement.childNodes[3].value = value;
    }
}
})