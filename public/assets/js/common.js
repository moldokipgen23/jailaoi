var baseUrl = jQuery("#base_url").val();

/************ Song upload ***************/
var datafile = new plupload.Uploader({
    runtimes: "html5,flash,silverlight,html4",
    browse_button: "uploadFile", // you can pass in id...
    container: document.getElementById("container"), // ... or DOM Element itself
    chunk_size: "1mb",
    url: baseUrl + "/admin/song/saveChunk",
    max_file_count: 1,
    unique_names: true,
    send_file_name: true,
    multi_selection: false,
    filters: {
        mime_types: [{ title: "Content files", extensions: "mp3" }],
        prevent_duplicates: true,
    },
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    init: {
        PostInit: function () {
            document.getElementById("filelist").innerHTML = "";
            document.getElementById("upload").onclick = function () {
                datafile.start();
                return false;
            };
        },
        FilesAdded: function (up, files) {
            while (up.files.length > 1) {
                up.removeFile(up.files[0]);
                document.getElementById("filelist2").innerHTML = "";
            }

            plupload.each(files, function (file) {
                document.getElementById("filelist").innerHTML +=
                    '<div id="' +
                    file.id +
                    '">' +
                    file.name +
                    " (" +
                    plupload.formatSize(file.size) +
                    ") <b></b></div>";

                var audio = document.createElement("audio");
                audio.preload = "metadata";

                audio.onloadedmetadata = function () {
                    URL.revokeObjectURL(audio.src);
                    var totalSeconds = Math.floor(audio.duration);

                    var hours = Math.floor(totalSeconds / 3600);
                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                    var seconds = totalSeconds % 60;

                    var foramttedDuration =
                        (hours < 10 ? "0" : "") +
                        hours +
                        ":" +
                        (minutes < 10 ? "0" : "") +
                        minutes +
                        ":" +
                        (seconds < 10 ? "0" : "") +
                        seconds;

                    jQuery("#timePicker").val(foramttedDuration);
                };

                audio.src = URL.createObjectURL(file.getNative());
            });
        },
        UploadProgress: function (up, file) {
            document
                .getElementById(file.id)
                .getElementsByTagName("b")[0].innerHTML =
                "<span>" + file.percent + "%</span>";
            if (file.percent > 60) {
                // jQuery('#song_url').val(file.name);
            }
        },
        FileUploaded: function (up, file, info) {
            jQuery("#song_url").val(file.target_name);
            var response = JSON.parse(info.response);
            if (response.result) {
                jQuery("#song_url").val(response.result);
            } else if (file.target_name) {
                jQuery("#song_url").val(file.target_name);
            }
            toastr.success("File Uploaded");
        },
        Error: function (up, err) {
            document.getElementById("console").innerHTML +=
                "\nError #" + err.code + ": " + err.message;
        },
    },
});
datafile.init();

/************ Podcast Episode upload ***************/
var datafile1 = new plupload.Uploader({
    runtimes: "html5,flash,silverlight,html4",
    browse_button: "uploadFile1", // you can pass in id...
    container: document.getElementById("container1"), // ... or DOM Element itself
    chunk_size: "1mb",
    url: baseUrl + "/admin/podcasts/episode/saveChunk",
    max_file_count: 1,
    unique_names: true,
    send_file_name: true,
    multi_selection: false,
    filters: {
        mime_types: [{ title: "Content files", extensions: "mp3" }],
        prevent_duplicates: true,
    },
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    init: {
        PostInit: function () {
            document.getElementById("filelist1").innerHTML = "";
            document.getElementById("upload1").onclick = function () {
                datafile1.start();
                return false;
            };
        },
        FilesAdded: function (up, files) {
            while (up.files.length > 1) {
                up.removeFile(up.files[0]);
                document.getElementById("filelist1").innerHTML = "";
            }

            plupload.each(files, function (file) {
                document.getElementById("filelist1").innerHTML +=
                    '<div id="' +
                    file.id +
                    '">' +
                    file.name +
                    " (" +
                    plupload.formatSize(file.size) +
                    ") <b></b></div>";

                var audio = document.createElement("audio");
                audio.preload = "metadata";

                audio.onloadedmetadata = function () {
                    URL.revokeObjectURL(audio.src);
                    var totalSeconds = Math.floor(audio.duration);

                    var hours = Math.floor(totalSeconds / 3600);
                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                    var seconds = totalSeconds % 60;

                    var foramttedDuration =
                        (hours < 10 ? "0" : "") +
                        hours +
                        ":" +
                        (minutes < 10 ? "0" : "") +
                        minutes +
                        ":" +
                        (seconds < 10 ? "0" : "") +
                        seconds;

                    jQuery("#timePicker").val(foramttedDuration);
                };

                audio.src = URL.createObjectURL(file.getNative());
            });
        },
        UploadProgress: function (up, file) {
            document
                .getElementById(file.id)
                .getElementsByTagName("b")[0].innerHTML =
                "<span>" + file.percent + "%</span>";
            if (file.percent > 60) {
                // jQuery('#episode_audio').val(file.name);
            }
        },
        FileUploaded: function (up, file, info) {
            var response = JSON.parse(info.response);
            if (response.result) {
                jQuery("#episode_audio").val(response.result);
            } else if (file.target_name) {
                jQuery("#episode_audio").val(file.target_name);
            }
            toastr.success("File Uploaded");
        },
        Error: function (up, err) {
            document.getElementById("console1").innerHTML +=
                "\nError #" + err.code + ": " + err.message;
        },
    },
});
datafile1.init();

/************ Music ************/
var datafile2 = new plupload.Uploader({
    runtimes: "html5,flash,silverlight,html4",
    browse_button: "uploadFile2", // you can pass in id...
    container: document.getElementById("container2"), // ... or DOM Element itself
    chunk_size: "1mb",
    url: baseUrl + "/admin/music/saveChunk",
    max_file_count: 1,
    unique_names: true,
    send_file_name: true,
    multi_selection: false,
    filters: {
        mime_types: [{ title: "Content files", extensions: "mp3" }],
        prevent_duplicates: true,
    },
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    init: {
        PostInit: function () {
            document.getElementById("filelist2").innerHTML = "";
            document.getElementById("upload2").onclick = function () {
                datafile2.start();
                return false;
            };
        },
        FilesAdded: function (up, files) {
            while (up.files.length > 1) {
                up.removeFile(up.files[0]);
                document.getElementById("filelist2").innerHTML = "";
            }

            plupload.each(files, function (file) {
                document.getElementById("filelist2").innerHTML +=
                    '<div id="' +
                    file.id +
                    '">' +
                    file.name +
                    " (" +
                    plupload.formatSize(file.size) +
                    ") <b></b></div>";

                var audio = document.createElement("audio");
                audio.preload = "metadata";

                audio.onloadedmetadata = function () {
                    URL.revokeObjectURL(audio.src);
                    var totalSeconds = Math.floor(audio.duration);

                    var hours = Math.floor(totalSeconds / 3600);
                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                    var seconds = totalSeconds % 60;

                    var foramttedDuration =
                        (hours < 10 ? "0" : "") +
                        hours +
                        ":" +
                        (minutes < 10 ? "0" : "") +
                        minutes +
                        ":" +
                        (seconds < 10 ? "0" : "") +
                        seconds;

                    jQuery("#timePicker").val(foramttedDuration);
                };

                audio.src = URL.createObjectURL(file.getNative());
            });
        },
        UploadProgress: function (up, file) {
            document
                .getElementById(file.id)
                .getElementsByTagName("b")[0].innerHTML =
                "<span>" + file.percent + "%</span>";
            if (file.percent > 60) {
                // jQuery('#music').val(file.name);
            }
        },
        FileUploaded: function (up, file, info) {
            var response = JSON.parse(info.response);
            if (response.result) {
                jQuery("#music_audio").val(response.result);
            } else if (file.target_name) {
                jQuery("#music_audio").val(file.target_name);
            }
            toastr.success("File Uploaded");
        },
        Error: function (up, err) {
            document.getElementById("console2").innerHTML +=
                "\nError #" + err.code + ": " + err.message;
        },
    },
});
datafile2.init();

/************ Podcast Add ***************/
var datafile3 = new plupload.Uploader({
    runtimes: "html5,flash,silverlight,html4",
    browse_button: "uploadFile3", // you can pass in id...
    container: document.getElementById("container3"), // ... or DOM Element itself
    chunk_size: "1mb",
    url: baseUrl + "/admin/podcasts/saveChunk",
    max_file_count: 1,
    unique_names: true,
    send_file_name: true,
    multi_selection: false,
    filters: {
        mime_types: [{ title: "Content files", extensions: "mp3" }],
        prevent_duplicates: true,
    },
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    init: {
        PostInit: function () {
            document.getElementById("filelist3").innerHTML = "";
            document.getElementById("upload3").onclick = function () {
                datafile3.start();
                return false;
            };
        },
        FilesAdded: function (up, files) {
            while (up.files.length > 1) {
                up.removeFile(up.files[0]);
                document.getElementById("filelist3").innerHTML = "";
            }

            plupload.each(files, function (file) {
                document.getElementById("filelist3").innerHTML +=
                    '<div id="' +
                    file.id +
                    '">' +
                    file.name +
                    " (" +
                    plupload.formatSize(file.size) +
                    ") <b></b></div>";

                var audio = document.createElement("audio");
                audio.preload = "metadata";

                audio.onloadedmetadata = function () {
                    URL.revokeObjectURL(audio.src);
                    var totalSeconds = Math.floor(audio.duration);

                    var hours = Math.floor(totalSeconds / 3600);
                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                    var seconds = totalSeconds % 60;

                    var foramttedDuration =
                        (hours < 10 ? "0" : "") +
                        hours +
                        ":" +
                        (minutes < 10 ? "0" : "") +
                        minutes +
                        ":" +
                        (seconds < 10 ? "0" : "") +
                        seconds;

                    jQuery("#timePicker").val(foramttedDuration);
                };

                audio.src = URL.createObjectURL(file.getNative());
            });
        },
        UploadProgress: function (up, file) {
            document
                .getElementById(file.id)
                .getElementsByTagName("b")[0].innerHTML =
                "<span>" + file.percent + "%</span>";
            if (file.percent > 60) {
                // jQuery('#trailer_audio').val(file.name);
            }
        },
        FileUploaded: function (up, file, info) {
            var response = JSON.parse(info.response);
            if (response.result) {
                jQuery("#trailer_audio").val(response.result);
            } else if (file.target_name) {
                jQuery("#trailer_audio").val(file.target_name);
            }
            toastr.success("File Uploaded");
        },
        Error: function (up, err) {
            document.getElementById("console1").innerHTML +=
                "\nError #" + err.code + ": " + err.message;
        },
    },
});
datafile3.init();

/************ Podcast Edit ***************/
var datafile4 = new plupload.Uploader({
    runtimes: "html5,flash,silverlight,html4",
    browse_button: "uploadFile4", // you can pass in id...
    container: document.getElementById("container4"), // ... or DOM Element itself
    chunk_size: "1mb",
    url: baseUrl + "/admin/podcasts/saveChunk",
    max_file_count: 1,
    unique_names: true,
    send_file_name: true,
    multi_selection: false,
    filters: {
        mime_types: [{ title: "Content files", extensions: "mp3" }],
        prevent_duplicates: true,
    },
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    init: {
        PostInit: function () {
            document.getElementById("filelist4").innerHTML = "";
            document.getElementById("upload4").onclick = function () {
                datafile4.start();
                return false;
            };
        },
        FilesAdded: function (up, files) {
            while (up.files.length > 1) {
                up.removeFile(up.files[0]);
                document.getElementById("filelist4").innerHTML = "";
            }

            plupload.each(files, function (file) {
                document.getElementById("filelist4").innerHTML +=
                    '<div id="' +
                    file.id +
                    '">' +
                    file.name +
                    " (" +
                    plupload.formatSize(file.size) +
                    ") <b></b></div>";

                var audio = document.createElement("audio");
                audio.preload = "metadata";

                audio.onloadedmetadata = function () {
                    URL.revokeObjectURL(audio.src);
                    var totalSeconds = Math.floor(audio.duration);

                    var hours = Math.floor(totalSeconds / 3600);
                    var minutes = Math.floor((totalSeconds % 3600) / 60);
                    var seconds = totalSeconds % 60;

                    var foramttedDuration =
                        (hours < 10 ? "0" : "") +
                        hours +
                        ":" +
                        (minutes < 10 ? "0" : "") +
                        minutes +
                        ":" +
                        (seconds < 10 ? "0" : "") +
                        seconds;

                    jQuery(".modal-body #edit_timePicker").val(
                        foramttedDuration
                    );
                };

                audio.src = URL.createObjectURL(file.getNative());
            });
        },
        UploadProgress: function (up, file) {
            document
                .getElementById(file.id)
                .getElementsByTagName("b")[0].innerHTML =
                "<span>" + file.percent + "%</span>";
            if (file.percent > 60) {
                // jQuery('#edit_trailer_audio').val(file.name);
            }
        },
        FileUploaded: function (up, file, info) {
            var response = JSON.parse(info.response);
            if (response.result) {
                jQuery("#edit_trailer_audio").val(response.result);
            } else if (file.target_name) {
                jQuery("#edit_trailer_audio").val(file.target_name);
            }
            toastr.success("File Uploaded");
        },
        Error: function (up, err) {
            document.getElementById("console1").innerHTML +=
                "\nError #" + err.code + ": " + err.message;
        },
    },
});
datafile4.init();
/**************************************/
