
$(document).ready(function () {

    const app = $.spapp({
        defaultView: "#home",
        templateDir: "",
        pageNotFound: false
    });

    function initializeFeatures() {
        if (typeof window.initializePageFeatures === "function") {
            window.initializePageFeatures();
        }
    }

    const pages = [
        "home",
        "shop",
        "about",
        "blog",
        "contact",
        "cart",
        "checkout"
    ];

    pages.forEach(function (page) {
        app.route({
            view: page,
            load: page + ".html",

            onCreate: function () {
                initializeFeatures();
            },

            onReady: function () {
                initializeFeatures();
            }
        });
    });

    function updateActiveNav() {
        const currentPage = window.location.hash || "#home";

        $(".custom-navbar-nav li").removeClass("active");

        $(".custom-navbar-nav a").each(function () {
            if ($(this).attr("href") === currentPage) {
                $(this).closest("li").addClass("active");
            }
        });
    }

    $(window).on("hashchange", function () {
        updateActiveNav();
    });

    app.run();

    updateActiveNav();

});
