
$(document).ready(function () {

    const app = $.spapp({
        defaultView: "#home",
        templateDir: "./",
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
            load: "pages/" + page + ".html",

            onCreate: function () {
                initializeFeatures();
            },

            onReady: function () {
                // Page already loaded; no need to initialize again.
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

    $(window).on("hashchange", updateActiveNav);

    app.run();
    updateActiveNav();

});
