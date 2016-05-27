/*
 * Copyright (C) 2016 Álvaro Orduna León
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    $('#content').css('margin-bottom', $('#page-footer').outerHeight() + 'px');

    var converter = new showdown.Converter();
    $('.markdown').each(function () {
        $(this).html(converter.makeHtml($(this).html()));
    });

    new SimpleMDE({
        element: document.getElementsByClassName("markdown-editor")[0],
        promptURLs: true,
        toolbar: ["bold", "italic", "code", "|",
            "link", "image", "|",
            "unordered-list", "ordered-list", "|",
            "preview", "clean-block", "|",
            "guide"]
    });
});

$(document).click(function (event) {
    if (!$(event.target).is('#menu-icon')) {
        if ($('#menu-icon').css('display') != 'none') {
            $('#menu-content').hide();
        }
    } else {
        $('#menu-content').toggle();
    }
});

window.onresize = function () {
    $('#content').css('margin-bottom', $('#page-footer').outerHeight() + 'px');

    if ($('#menu-icon').css('display') == 'none') {
        $('#menu-content').show();
    } else {
        $('#menu-content').hide();
    }
};

$.extend(
    {
        redirectPost: function (location, args) {
            var form = '';
            $.each(args, function (key, value) {
                value = value.split('"').join('\"');
                form += '<input type="hidden" name="' + key + '" value="' + value + '">';
            });
            $('<form action="' + location + '" method="POST">' + form + '</form>').appendTo($(document.body)).submit();
        }
    });
