(function ($) {
  "use strict";




  /*--------------------------------------------------------------
    RegisterPlugin, ScrollTrigger, SplitText
  --------------------------------------------------------------*/
  gsap.registerPlugin(ScrollTrigger, SplitText);
  gsap.config({
    nullTargetWarn: false,
    trialWarn: false
  });



  // Preloader
  $('#preloader').delay(200).fadeOut('slow');


  // AOS Animation
  if ($("[data-aos]").length) {
    AOS.init({
      duration: '1200',
      disable: 'false',
      easing: 'ease',
      mirror: true
    });
  }


  // ===Datepicker===
  if ($("#datepicker").length) {
    $("#datepicker").datepicker();
  }


  /*--------------------------------------------------------------
    FullHeight
  --------------------------------------------------------------*/
  function fullHeight() {
    $('.full-height').css("height", $(window).height());
  }




  function thmSwiperInit() {
    // swiper slider
    if ($(".thm-swiper__slider").length) {
      $(".thm-swiper__slider").each(function () {
        let elm = $(this);
        let options = elm.data('swiper-options');
        let thmSwiperSlider = new Swiper(elm, options);
      });
    }

  }



  // Services One Carousel
  if ($(".services-one__carousel").length > 0) {
    const swiper = new Swiper('.services-one__carousel', {
      "slidesPerView": 1,
      "spaceBetween": 0,
      "speed": 600,
      "loop": true,
      "pagination": {
        "el": "#swiper-dot-style1",
        "type": "bullets",
        "clickable": true
      },

      "navigation": {
        "nextEl": "#swiper-button-prev1",
        "prevEl": "#swiper-button-next1"
      },
      "autoplay": {
        "delay": 6000
      },
      "breakpoints": {
        "0": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "375": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "575": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "768": {
          "spaceBetween": 30,
          "slidesPerView": 2
        },
        "992": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "1200": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "1320": {
          "spaceBetween": 30,
          "slidesPerView": 3
        }
      },
    });
  }

  if ($(".marquee_mode-1").length) {
    $('.marquee_mode-1').marquee({
      speed: 30,
      gap: 0,
      delayBeforeStart: 0,
      direction: 'left',
      duplicated: true,
      pauseOnHover: true,
      startVisible: true,
    });
  }

  if ($(".marquee_mode-2").length) {
    $('.marquee_mode-2').marquee({
      speed: 30,
      gap: 20,
      delayBeforeStart: 0,
      direction: 'left',
      duplicated: true,
      pauseOnHover: true,
      startVisible: true,
    });
  }
  if ($(".marquee_mode-3").length) {
    $('.marquee_mode-3').marquee({
      speed: 30,
      gap: 30,
      delayBeforeStart: 0,
      direction: 'left',
      duplicated: true,
      pauseOnHover: true,
      startVisible: true,
    });
  }



  // Brand One Carousel
  if ($(".brand-one__carousel").length > 0) {
    const swiper = new Swiper('.brand-one__carousel', {
      "slidesPerView": 5,
      "spaceBetween": 0,
      "speed": 600,
      "loop": true,
      "pagination": {
        "el": "#swiper-dot-style1",
        "type": "bullets",
        "clickable": true
      },

      "navigation": {
        "nextEl": "#swiper-button-prev1",
        "prevEl": "#swiper-button-next1"
      },
      "autoplay": {
        "delay": 8000
      },
      "breakpoints": {
        "0": {
          "spaceBetween": 0,
          "slidesPerView": 1
        },
        "375": {
          "spaceBetween": 0,
          "slidesPerView": 1
        },
        "575": {
          "spaceBetween": 0,
          "slidesPerView": 1
        },
        "768": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "992": {
          "spaceBetween": 30,
          "slidesPerView": 4
        },
        "1200": {
          "spaceBetween": 30,
          "slidesPerView": 5
        },
        "1320": {
          "spaceBetween": 30,
          "slidesPerView": 5
        }
      },
    });
  }


  // Project One
  if ($(".project-one__carousel").length > 0) {
    const swiper = new Swiper('.project-one__carousel', {
      "slidesPerView": 3,
      "spaceBetween": 0,
      "speed": 600,
      "loop": true,
      "pagination": {
        "el": ".swiper-dot-style1",
        "type": "bullets",
        "clickable": true
      },

      "navigation": {
        "nextEl": ".swiper-button-prev1",
        "prevEl": ".swiper-button-next1"
      },
      "autoplay": {
        "delay": 6000
      },
      "breakpoints": {
        "0": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "375": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "575": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "768": {
          "spaceBetween": 30,
          "slidesPerView": 2
        },
        "992": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "1200": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "1320": {
          "spaceBetween": 30,
          "slidesPerView": 3
        }
      },
    });
  }

  // Testimonial One
  if ($(".testimonial-one__carousel").length > 0) {
    const swiper = new Swiper('.testimonial-one__carousel', {
      "slidesPerView": 3,
      "spaceBetween": 0,
      "speed": 600,
      "loop": true,
      "pagination": {
        "el": ".swiper-dot-style1",
        "type": "bullets",
        "clickable": true
      },

      "navigation": {
        "nextEl": ".swiper-button-prev1",
        "prevEl": ".swiper-button-next1"
      },
      "autoplay": {
        "delay": 6000
      },
      "breakpoints": {
        "0": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "375": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "575": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "768": {
          "spaceBetween": 30,
          "slidesPerView": 2
        },
        "992": {
          "spaceBetween": 30,
          "slidesPerView": 2
        },
        "1200": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "1320": {
          "spaceBetween": 30,
          "slidesPerView": 3
        }
      },
    });
  }

  // Gallery One
  if ($(".gallery-one__carousel").length > 0) {
    const swiper = new Swiper('.gallery-one__carousel', {
      "slidesPerView": 6,
      "spaceBetween": 0,
      "speed": 2000,
      "loop": true,
      "pagination": {
        "el": ".swiper-dot-style1",
        "type": "bullets",
        "clickable": true
      },

      "navigation": {
        "nextEl": ".swiper-button-prev1",
        "prevEl": ".swiper-button-next1"
      },
      "autoplay": {
        "delay": 8000
      },
      "breakpoints": {
        "0": {
          "spaceBetween": 0,
          "slidesPerView": 1
        },
        "375": {
          "spaceBetween": 0,
          "slidesPerView": 1
        },
        "575": {
          "spaceBetween": 5,
          "slidesPerView": 2
        },
        "768": {
          "spaceBetween": 5,
          "slidesPerView": 3
        },
        "992": {
          "spaceBetween": 5,
          "slidesPerView": 4
        },
        "1200": {
          "spaceBetween": 5,
          "slidesPerView": 5
        },
        "1320": {
          "spaceBetween": 5,
          "slidesPerView": 6
        }
      },
    });
  }

  //Project Two Carousel
  if ($(".project-two__carousel").length) {
    $(".project-two__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      navText: [
        '<span class="icon-arrow-right"></span>',
        '<span class="icon-arrow-right"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1320: {
          items: 4,
        },
      },
    });
  }

  // Testimonial Two
  if ($(".testimonial-two__carousel").length > 0) {
    const swiper = new Swiper('.testimonial-two__carousel', {
      "slidesPerView": 3,
      "spaceBetween": 0,
      "speed": 600,
      "loop": true,
      "pagination": {
        "el": ".swiper-dot-style1",
        "type": "bullets",
        "clickable": true
      },

      "navigation": {
        "nextEl": ".swiper-button-prev1",
        "prevEl": ".swiper-button-next1"
      },
      "autoplay": {
        "delay": 6000
      },
      "breakpoints": {
        "0": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "375": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "575": {
          "spaceBetween": 20,
          "slidesPerView": 1
        },
        "768": {
          "spaceBetween": 30,
          "slidesPerView": 2
        },
        "992": {
          "spaceBetween": 20,
          "slidesPerView": 2
        },
        "1200": {
          "spaceBetween": 30,
          "slidesPerView": 3
        },
        "1320": {
          "spaceBetween": 30,
          "slidesPerView": 3
        }
      },
    });
  }

  //Services Three Carousel
  if ($(".service-three__carousel").length) {
    $(".service-three__carousel").owlCarousel({
      loop: true,
      margin: 15,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      navText: [
        '<span class="icon-left-arrow"></span>',
        '<span class="icon-right-arrow"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 2,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    });
  }


  //Feature Two Carousel
  if ($(".feature-two__carousel").length) {
    $(".feature-two__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      navText: [
        '<span class="icon-left-arrow"></span>',
        '<span class="icon-right-arrow"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
        1320: {
          items: 4,
        },
      },
    });
  }


  //Services Three Tab Tabs
  if ($('.service-three-tabs-box').length) {
    $('.service-three-tabs-box .service-three-tab-btns .p-tab-btn').on('click', function (e) {
      e.preventDefault();
      var target = $($(this).attr('data-tab'));

      if ($(target).hasClass('actve-tab')) {
        return false;
      } else {
        $('.service-three-tabs-box .service-three-tab-btns .p-tab-btn').removeClass('active-btn');
        $(this).addClass('active-btn');
        $('.service-three-tabs-box .p-tabs-content .p-tab').removeClass('active-tab');
        $(target).addClass('active-tab');
      }
    });
  }





  //Image Reveal Animation
  if ($(".reveal").length) {
    gsap.registerPlugin(ScrollTrigger);
    let revealContainers = document.querySelectorAll(".reveal");
    revealContainers.forEach((container) => {
      let image = container.querySelector("img");
      let tl = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          toggleActions: "play none none none",
        },
      });
      tl.set(container, {
        autoAlpha: 1,
      });
      tl.from(container, 1.5, {
        xPercent: -100,
        ease: Power2.out,
      });
      tl.from(image, 1.5, {
        xPercent: 100,
        scale: 1.3,
        delay: -1.5,
        ease: Power2.out,
      });
    });
  }




  // custom coursor
  if ($(".custom-cursor").length) {

    var cursor = document.querySelector('.custom-cursor__cursor');
    var cursorinner = document.querySelector('.custom-cursor__cursor-two');
    var a = document.querySelectorAll('a');

    document.addEventListener('mousemove', function (e) {
      var x = e.clientX;
      var y = e.clientY;
      cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`
    });

    document.addEventListener('mousemove', function (e) {
      var x = e.clientX;
      var y = e.clientY;
      cursorinner.style.left = x + 'px';
      cursorinner.style.top = y + 'px';
    });

    document.addEventListener('mousedown', function () {
      cursor.classList.add('click');
      cursorinner.classList.add('custom-cursor__innerhover')
    });

    document.addEventListener('mouseup', function () {
      cursor.classList.remove('click')
      cursorinner.classList.remove('custom-cursor__innerhover')
    });

    a.forEach(item => {
      item.addEventListener('mouseover', () => {
        cursor.classList.add('custom-cursor__hover');
      });
      item.addEventListener('mouseleave', () => {
        cursor.classList.remove('custom-cursor__hover');
      });
    })
  }








  //Progress Count Bar
  if ($(".count-bar").length) {
    $(".count-bar").appear(
      function () {
        var el = $(this);
        var percent = el.data("percent");
        $(el).css("width", percent).addClass("counted");
      }, {
        accY: -50
      }
    );
  }



  //Fact Counter + Text Count
  if ($(".count-box").length) {
    $(".count-box").appear(
      function () {
        var $t = $(this),
          n = $t.find(".count-text").attr("data-stop"),
          r = parseInt($t.find(".count-text").attr("data-speed"), 10);

        if (!$t.hasClass("counted")) {
          $t.addClass("counted");
          $({
            countNum: $t.find(".count-text").text()
          }).animate({
            countNum: n
          }, {
            duration: r,
            easing: "linear",
            step: function () {
              $t.find(".count-text").text(Math.floor(this.countNum));
            },
            complete: function () {
              $t.find(".count-text").text(this.countNum);
            }
          });
        }
      }, {
        accY: 0
      }
    );
  }




  // Accrodion
  if ($(".accrodion-grp").length) {
    var accrodionGrp = $(".accrodion-grp");
    accrodionGrp.each(function () {
      var accrodionName = $(this).data("grp-name");
      var Self = $(this);
      var accordion = Self.find(".accrodion");
      Self.addClass(accrodionName);
      Self.find(".accrodion .accrodion-content").hide();
      Self.find(".accrodion.active").find(".accrodion-content").show();
      accordion.each(function () {
        $(this)
          .find(".accrodion-title")
          .on("click", function () {
            if ($(this).parent().hasClass("active") === false) {
              $(".accrodion-grp." + accrodionName)
                .find(".accrodion")
                .removeClass("active");
              $(".accrodion-grp." + accrodionName)
                .find(".accrodion")
                .find(".accrodion-content")
                .slideUp();
              $(this).parent().addClass("active");
              $(this).parent().find(".accrodion-content").slideDown();
            }
          });
      });
    });
  }



  $(".contact-form-validated").each(function () {
    $(this).validate({
      rules: {
        email: {
          required: true,
          email: true
        }
      },
      submitHandler: function (form) {
        $.post(
          $(form).attr("action"),
          $(form).serialize(),
          function (response) {
            $(form).find(".result").html(response);
            $(form).find('input[type="text"], input[type="email"], textarea').val("");
          }
        );
        return false;
      }
    });
  });







  if ($(".video-popup").length) {
    $(".video-popup").magnificPopup({
      type: "iframe",
      mainClass: "mfp-fade",
      removalDelay: 160,
      preloader: true,

      fixedContentPos: false
    });
  }

  if ($(".img-popup").length) {
    var groups = {};
    $(".img-popup").each(function () {
      var id = parseInt($(this).attr("data-group"), 10);

      if (!groups[id]) {
        groups[id] = [];
      }

      groups[id].push(this);
    });

    $.each(groups, function () {
      $(this).magnificPopup({
        type: "image",
        closeOnContentClick: true,
        closeBtnInside: false,
        gallery: {
          enabled: true
        }
      });
    });
  }



  //Chat Popup
  if ($('#chat-popup').length) {

    //Show Popup
    $('.chat-toggler').on('click', function () {
      $('#chat-popup').addClass('popup-visible');
    });
    $(document).keydown(function (e) {
      if (e.keyCode === 27) {
        $('#chat-popup').removeClass('popup-visible');
      }
    });
    //Hide Popup
    $('.close-chat,.chat-popup .overlay-layer').on('click', function () {
      $('#chat-popup').removeClass('popup-visible');
    });
  }




  function dynamicCurrentMenuClass(selector) {
    let fileName = window.location.pathname.split("/").pop() || "index.html";

    // Remove all existing 'current' classes
    selector.find("li").removeClass("current");

    // Loop through all <li> elements and match with href
    selector.find("li").each(function () {
      let anchor = $(this).find("a").first();
      if (anchor.length && anchor.attr("href") === fileName) {
        $(this).addClass("current");

        // Traverse all parent <li> and add 'current'
        $(this).parents("li").addClass("current");
      }
    });

    // Default: if no current found, highlight the Home
    if (!selector.find("li.current").length) {
      selector.children("li").first().addClass("current");
    }
  }

  // Initialize
  if ($(".main-menu__list").length) {
    let mainNavUL = $(".main-menu__list");
    dynamicCurrentMenuClass(mainNavUL);
  }




  if ($(".main-menu__list").length && $(".mobile-nav__container").length) {
    let navContent = document.querySelector(".main-menu__list").outerHTML;
    let mobileNavContainer = document.querySelector(".mobile-nav__container");
    mobileNavContainer.innerHTML = navContent;
  }
  if ($(".sticky-header__content").length) {
    let navContent = document.querySelector(".main-menu").innerHTML;
    let mobileNavContainer = document.querySelector(".sticky-header__content");
    mobileNavContainer.innerHTML = navContent;
  }

  if ($(".mobile-nav__container .main-menu__list").length) {
    let dropdownAnchor = $(
      ".mobile-nav__container .main-menu__list .dropdown > a"
    );
    dropdownAnchor.each(function () {
      let self = $(this);
      let toggleBtn = document.createElement("BUTTON");
      toggleBtn.setAttribute("aria-label", "dropdown toggler");
      toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
      self.append(function () {
        return toggleBtn;
      });
      self.find("button").on("click", function (e) {
        e.preventDefault();
        let self = $(this);
        self.toggleClass("expanded");
        self.parent().toggleClass("expanded");
        self.parent().parent().children("ul").slideToggle();
      });
    });
  }

  if ($(".mobile-nav__toggler").length) {
    $(".mobile-nav__toggler").on("click", function (e) {
      e.preventDefault();
      $(".mobile-nav__wrapper").toggleClass("expanded");
      $("body").toggleClass("locked");
    });
  }




  //Header Search
  if ($('.searcher-toggler-box').length) {
    $('.searcher-toggler-box').on('click', function (e) {
      e.preventDefault();
      $('body').addClass('search-active');
    });


    $('.close-search').on('click', function () {
      $('body').removeClass('search-active');
    });


    $('.search-popup .color-layer').on('click', function () {
      $('body').removeClass('search-active');
    });

  }




  if ($(".wow").length) {
    var wow = new WOW({
      boxClass: "wow", // animated element css class (default is wow)
      animateClass: "animated", // animation css class (default is animated)
      mobile: true, // trigger animations on mobile devices (default is true)
      live: true // act on asynchronously loaded content (default is true)
    });
    wow.init();
  }






  if ($(".tabs-box").length) {
    $(".tabs-box .tab-buttons .tab-btn").on("click", function (e) {
      e.preventDefault();
      var target = $($(this).attr("data-tab"));

      if ($(target).is(":visible")) {
        return false;
      } else {
        target
          .parents(".tabs-box")
          .find(".tab-buttons")
          .find(".tab-btn")
          .removeClass("active-btn");
        $(this).addClass("active-btn");
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .fadeOut(0);
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .removeClass("active-tab");
        $(target).fadeIn(300);
        $(target).addClass("active-tab");
      }
    });
  }


  if ($(".odometer").length) {
    var odo = $(".odometer");
    odo.each(function () {
      $(this).appear(function () {
        var countNumber = $(this).attr("data-count");
        $(this).html(countNumber);
      });
    });
  }









  // ===Portfolio===
  function projectMasonaryLayout() {
    if ($(".masonary-layout").length) {
      $(".masonary-layout").isotope({
        layoutMode: "masonry"
      });
    }
    if ($(".post-filter").length) {
      $(".post-filter li")
        .children(".filter-text")
        .on("click", function () {
          var Self = $(this);
          var selector = Self.parent().attr("data-filter");
          $(".post-filter li").removeClass("active");
          Self.parent().addClass("active");
          $(".filter-layout").isotope({
            filter: selector,
            animationOptions: {
              duration: 500,
              easing: "linear",
              queue: false
            }
          });
          return false;
        });
    }

    if ($(".post-filter.has-dynamic-filters-counter").length) {
      // var allItem = $('.single-filter-item').length;
      var activeFilterItem = $(".post-filter.has-dynamic-filters-counter").find(
        "li"
      );
      activeFilterItem.each(function () {
        var filterElement = $(this).data("filter");
        var count = $(".filter-layout").find(filterElement).length;
        $(this)
          .children(".filter-text")
          .append('<span class="count">' + count + "</span>");
      });
    }
  }













  function SmoothMenuScroll() {
    var anchor = $(".scrollToLink");
    if (anchor.length) {
      anchor.children("a").bind("click", function (event) {
        if ($(window).scrollTop() > 10) {
          var headerH = "90";
        } else {
          var headerH = "90";
        }
        var target = $(this);
        $("html, body")
          .stop()
          .animate({
              scrollTop: $(target.attr("href")).offset().top - headerH + "px"
            },
            200,
            "easeInOutExpo"
          );
        anchor.removeClass("current");
        anchor.removeClass("current-menu-ancestor");
        anchor.removeClass("current_page_item");
        anchor.removeClass("current-menu-parent");
        target.parent().addClass("current");
        event.preventDefault();
      });
    }
  }
  SmoothMenuScroll();

  function OnePageMenuScroll() {
    var windscroll = $(window).scrollTop();
    if (windscroll >= 117) {
      var menuAnchor = $(".one-page-scroll-menu .scrollToLink").children("a");
      menuAnchor.each(function () {
        var sections = $(this).attr("href");
        $(sections).each(function () {
          if ($(this).offset().top <= windscroll + 100) {
            var Sectionid = $(sections).attr("id");
            $(".one-page-scroll-menu").find("li").removeClass("current");
            $(".one-page-scroll-menu").find("li").removeClass("current-menu-ancestor");
            $(".one-page-scroll-menu").find("li").removeClass("current_page_item");
            $(".one-page-scroll-menu").find("li").removeClass("current-menu-parent");
            $(".one-page-scroll-menu")
              .find("a[href*=\\#" + Sectionid + "]")
              .parent()
              .addClass("current");
          }
        });
      });
    } else {
      $(".one-page-scroll-menu li.current").removeClass("current");
      $(".one-page-scroll-menu li:first").addClass("current");
    }
  }






  /*-- Handle Scrollbar --*/
  function handleScrollbar() {
    const bodyHeight = $("body").height();
    const scrollPos = $(window).innerHeight() + $(window).scrollTop();
    let percentage = (scrollPos / bodyHeight) * 100;
    if (percentage > 100) {
      percentage = 100;
    }
    $(".scroll-to-top .scroll-to-top__inner").css("width", percentage + "%");
  }




  // Animation gsap 
  function title_animation() {
    var tg_var = jQuery('.sec-title-animation');
    if (!tg_var.length) {
      return;
    }
    const quotes = document.querySelectorAll(".sec-title-animation .title-animation");

    quotes.forEach(quote => {

      //Reset if needed
      if (quote.animation) {
        quote.animation.progress(1).kill();
        quote.split.revert();
      }

      var getclass = quote.closest('.sec-title-animation').className;
      var animation = getclass.split('animation-');
      if (animation[1] == "style4") return

      quote.split = new SplitText(quote, {
        type: "lines,words,chars",
        linesClass: "split-line"
      });
      gsap.set(quote, {
        perspective: 400
      });

      if (animation[1] == "style1") {
        gsap.set(quote.split.chars, {
          opacity: 0,
          y: "90%",
          rotateX: "-40deg"
        });
      }
      if (animation[1] == "style2") {
        gsap.set(quote.split.chars, {
          opacity: 0,
          x: "50"
        });
      }
      if (animation[1] == "style3") {
        gsap.set(quote.split.chars, {
          opacity: 0,
        });
      }
      quote.animation = gsap.to(quote.split.chars, {
        scrollTrigger: {
          trigger: quote,
          start: "top 90%",
        },
        x: "0",
        y: "0",
        rotateX: "0",
        opacity: 1,
        duration: 1,
        ease: Back.easeOut,
        stagger: .02
      });
    });
  }
  ScrollTrigger.addEventListener("refresh", title_animation);






  // ===Price Filter===
  function priceFilter() {
    if ($(".price-ranger").length) {
      $(".price-ranger #slider-range").slider({
        range: true,
        min: 0,
        max: 5000,
        values: [0, 3000],
        slide: function (event, ui) {
          $(".price-ranger .ranger-min-max-block .min").val("" + ui.values[0]);
          $(".price-ranger .ranger-min-max-block .max").val("" + ui.values[1]);
        },
      });
      $(".price-ranger .ranger-min-max-block .min").val(
        "" + $(".price-ranger #slider-range").slider("values", 0)
      );
      $(".price-ranger .ranger-min-max-block .max").val(
        "" + $(".price-ranger #slider-range").slider("values", 1)
      );
    }
  }



  $(".add").on("click", function () {
    if ($(this).prev().val() < 999) {
      $(this)
        .prev()
        .val(+$(this).prev().val() + 1);
    }
  });
  $(".sub").on("click", function () {
    if ($(this).next().val() > 1) {
      if ($(this).next().val() > 1)
        $(this)
        .next()
        .val(+$(this).next().val() - 1);
    }
  });






  // ===Checkout Payment===
  if ($(".checkout__payment__title").length) {
    $(".checkout__payment__item").find(".checkout__payment__content").hide();
    $(".checkout__payment__item--active").find(".checkout__payment__content").show();

    $(".checkout__payment__title").on("click", function (e) {
      e.preventDefault();

      $(this)
        .parents(".checkout__payment")
        .find(".checkout__payment__item")
        .removeClass("checkout__payment__item--active");
      $(this).parents(".checkout__payment").find(".checkout__payment__content").slideUp();

      $(this).parent().addClass("checkout__payment__item--active");
      $(this).parent().find(".checkout__payment__content").slideDown();
    });
  }





  // Product All Tab
  if ($(".product__all-tab").length) {
    $(".product__all-tab .tabs-button-box .tab-btn-item").on("click", function (e) {
      e.preventDefault();
      var target = $($(this).attr("data-tab"));

      if ($(target).hasClass("actve-tab")) {
        return false;
      } else {
        $(".product__all-tab .tabs-button-box .tab-btn-item").removeClass("active-btn-item");
        $(this).addClass("active-btn-item");
        $(".product__all-tab .tabs-content-box .tab-content-box-item").removeClass(
          "tab-content-box-item-active"
        );
        $(target).addClass("tab-content-box-item-active");
      }
    });
  }





  if ($("#shop-details-one__thumb").length) {
    let testimonialsThumb = new Swiper("#shop-details-one__thumb", {
      slidesPerView: 3,
      spaceBetween: 0,
      speed: 1400,
      watchSlidesVisibility: true,
      watchSlidesProgress: true,
      loop: true,
      autoplay: {
        delay: 5000
      }
    });

    let testimonialsCarousel = new Swiper("#shop-details-one__carousel", {
      observer: true,
      observeParents: true,
      loop: true,
      speed: 1400,
      mousewheel: false,
      slidesPerView: 1,
      autoplay: {
        delay: 5000
      },
      thumbs: {
        swiper: testimonialsThumb
      },
      pagination: {
        el: '#testimonials-one__carousel-pagination',
        type: 'bullets',
        clickable: true
      },

      "navigation": {
        "nextEl": "#product-details__swiper-button-next",
        "prevEl": "#product-details__swiper-button-prev"
      },
    });
  }



  //Related Products Carousel
  if ($(".related-products__carousel").length) {
    $(".related-products__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 2000,
      navText: [
        '<span class="icon-left-arrow"></span>',
        '<span class="icon-right-arrow"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 4,
        },
      },
    });
  }






  if ($("#testimonial-three__thumb").length) {
    let testimonialsThumb = new Swiper("#testimonial-three__thumb", {
      slidesPerView: 3,
      spaceBetween: 0,
      speed: 1400,
      watchSlidesVisibility: true,
      watchSlidesProgress: true,
      slideToClickedSlide: true,
      loopedSlides: 4,
      direction: 'vertical',
      loop: true,
      autoplay: {
        delay: 5000
      },
      breakpoints: {
        0: {
          slidesPerView: 3,
          spaceBetween: 0
        },
        575: {
          slidesPerView: 3,
          spaceBetween: 0
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 0
        },

      }
    });

    let testimonialsCarousel = new Swiper("#testimonial-three__carousel", {
      observer: true,
      observeParents: true,
      loop: true,
      speed: 1400,
      mousewheel: false,
      slidesPerView: 1,
      spaceBetween: 72,
      autoplay: {
        delay: 5000
      },
      thumbs: {
        swiper: testimonialsThumb
      },
      pagination: {
        el: "#testimonial-three__carousel-pagination",
        type: "bullets",
        clickable: true
      },

      navigation: {
        nextEl: "#testimonial-three__swiper-button-next",
        prevEl: "#testimonial-three__swiper-button-prev"
      }
    });
  }










  // window load event
  $(window).on("load", function () {


    projectMasonaryLayout();
    fullHeight();
    title_animation();
    priceFilter();
    thmSwiperInit();








    if ($(".post-filter").length) {
      var postFilterList = $(".post-filter li");
      // for first init
      $(".filter-layout").isotope({
        filter: ".filter-item",
        animationOptions: {
          duration: 500,
          easing: "linear",
          queue: false
        }
      });
      // on click filter links
      postFilterList.on("click", function () {
        var Self = $(this);
        var selector = Self.attr("data-filter");
        postFilterList.removeClass("active");
        Self.addClass("active");

        $(".filter-layout").isotope({
          filter: selector,
          animationOptions: {
            duration: 500,
            easing: "linear",
            queue: false
          }
        });
        return false;
      });
    }

    if ($(".post-filter.has-dynamic-filter-counter").length) {
      // var allItem = $('.single-filter-item').length;

      var activeFilterItem = $(".post-filter.has-dynamic-filter-counter").find(
        "li"
      );

      activeFilterItem.each(function () {
        var filterElement = $(this).data("filter");
        var count = $(".filter-layout").find(filterElement).length;
        $(this).append("<sup>[" + count + "]</sup>");
      });
    }








    if ($(".circle-progress").length) {
      $(".circle-progress").appear(function () {
        let circleProgress = $(".circle-progress");
        circleProgress.each(function () {
          let progress = $(this);
          let progressOptions = progress.data("options");
          progress.circleProgress(progressOptions);
        });
      });
    }













  });




  // window scroll event

  $(window).on("scroll", function () {
    if ($(".stricked-menu").length) {
      var headerScrollPos = 300;
      var stricky = $(".stricked-menu");
      if ($(window).scrollTop() > headerScrollPos) {
        stricky.addClass("stricky-fixed");
      } else if ($(this).scrollTop() <= headerScrollPos) {
        stricky.removeClass("stricky-fixed");
      }
    }

    OnePageMenuScroll();

  });

  $(window).on("scroll", function () {
    handleScrollbar();
    if ($(".sticky-header--one-page").length) {
      var headerScrollPos = 130;
      var stricky = $(".sticky-header--one-page");
      if ($(window).scrollTop() > headerScrollPos) {
        stricky.addClass("active");
      } else if ($(this).scrollTop() <= headerScrollPos) {
        stricky.removeClass("active");
      }
    }

    var scrollToTopBtn = ".scroll-to-top";
    if (scrollToTopBtn.length) {
      if ($(window).scrollTop() > 500) {
        $(scrollToTopBtn).addClass("show");
      } else {
        $(scrollToTopBtn).removeClass("show");
      }
    }
  });




  if ($(".before-after-twentytwenty").length) {
    $(".before-after-twentytwenty").each(function () {
      var Self = $(this);
      var objName = Self.attr("id");
      $("#" + objName).twentytwenty();

      // hack for bs tab
      $(document).on("shown.bs.tab", 'a[data-toggle="tab"]', function (e) {
        var paneTarget = $(e.target).attr("data-target");
        var $thePane = $(".tab-pane" + paneTarget);
        var twentyTwentyContainer = "#" + objName;
        var twentyTwentyHeight = $thePane.find(twentyTwentyContainer).height();
        if (0 === twentyTwentyHeight) {
          $thePane.find(twentyTwentyContainer).trigger("resize");
        }
      });
    });
  }


  $('select:not(.ignore)').niceSelect();











})(jQuery);