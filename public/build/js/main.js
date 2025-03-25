

$(function () {
  "use strict";


  /* scrollar */

  new PerfectScrollbar(".notify-list")

  // new PerfectScrollbar(".search-content")

  // new PerfectScrollbar(".mega-menu-widgets")



  /* toggle button */

  $(".btn-toggle").click(function () {
    $("body").hasClass("toggled") ? ($("body").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($("body").addClass("toggled"), $(".sidebar-wrapper").hover(function () {
      $("body").addClass("sidebar-hovered")
    }, function () {
      $("body").removeClass("sidebar-hovered")
    }))
  })




  /* menu */

  $(function () {
    $('#sidenav').metisMenu();
  });

  $(".sidebar-close").on("click", function () {
    $("body").removeClass("toggled")
  })



// Betöltéskor ellenőrizzük a mentett témát
    const savedTheme = localStorage.getItem('theme') || 'light';
    $("html").attr("data-bs-theme", savedTheme);
    $(".dark-mode i").text(savedTheme === 'dark' ? 'light_mode' : 'dark_mode');

// Dark mode ikon kattintás kezelése
    $(".dark-mode i").click(function () {
        $(this).text(function (i, v) {
            return v === 'dark_mode' ? 'light_mode' : 'dark_mode'
        });
    });

// Dark mode kapcsoló kattintás kezelése
    $(".dark-mode").click(function () {
        $("html").attr("data-bs-theme", function (i, v) {
            const newTheme = v === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            document.cookie = "theme=" + localStorage.getItem('theme');

            return newTheme;
        });
    });



  /* switcher */

  $("#LightTheme").on("click", function () {
      console.log('light')
    $("html").attr("data-bs-theme", "light")
  }),

    $("#DarkTheme").on("click", function () {
        console.log('dark')
      $("html").attr("data-bs-theme", "dark")
    }),

    $("#SemiDarkTheme").on("click", function () {
      $("html").attr("data-bs-theme", "semi-dark")
    }),

    $("#BoderedTheme").on("click", function () {
      $("html").attr("data-bs-theme", "bodered-theme")
    })



  /* search control */

  $(".search-control").click(function () {
    $(".search-popup").addClass("d-block");
    $(".search-close").addClass("d-block");
  });


  $(".search-close").click(function () {
    $(".search-popup").removeClass("d-block");
    $(".search-close").removeClass("d-block");
  });


  $(".mobile-search-btn").click(function () {
    $(".search-popup").addClass("d-block");
  });


  $(".mobile-search-close").click(function () {
    $(".search-popup").removeClass("d-block");
  });




  /* menu active */

  $(function () {
    for (var e = window.location, o = $(".metismenu li a").filter(function () {
      return this.href == e
    }).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
  });



});










