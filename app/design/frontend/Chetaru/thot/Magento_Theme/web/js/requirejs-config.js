(function (require) {
    var config = {
        paths: {
            "jquery.bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min",
            "fancybox": "js/fancybox3/fancybox.min",
            "magnify": "js/magnify",
            "fotorama": "js/fotorama.min"
        },
        shim: {
            "jquery.bootstrap": {
                deps: ["jquery"]
            },
            "magnify": {
                deps: ["jquery"]
            },
            "fotorama": {
                deps: ["jquery"]
            }
        }
    };

    require.config(config);
})(require);
