/**
 * Submit form via AJAX request 
 * 
 * @param {string} formSelector
 * @param {string} responseMethod
 * @param {string} actionUrl
 * @returns {undefined}
 */
function submitFormAjax(formSelector, responseMethod, actionUrl) {
    var form = $(formSelector);
    if (form.length) {
        var submitButtonValue = $("input[type='submit']", form).val();
        form.submit(function () {
            $("input[type='submit']", form).val("Please Wait...")
                    .attr('disabled', 'disabled');
        });
        form.submit(function (event) {
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(), // serializes the form's elements.
                success: function (data)
                {
                    if (data.isValid === true) {
                        window[responseMethod](data);
                    } else {
                        displayError(data);
                    }
                    $("input[type='submit']", form).val(submitButtonValue).prop('disabled', false);
                }
            });

            event.preventDefault(); // avoid to execute the actual submit of the form.
        });
    }
}

/**
 * Display submitted comment
 * 
 * @param {object} data submission result
 * @returns {undefined}
 */
function displayComment(data) {
    var commentHtml = createComment(data.data);
    var comment = $(commentHtml);
    comment.hide();
    var postList = $('#posts-list_' + data.data.postId);
    postList.addClass('has-comments');
    postList.prepend(comment);
    comment.slideDown();
    linkifyText();
}

/**
 * Display submitted post
 * 
 * @param {object} data submission result
 * @returns {undefined}
 */
function displayPost(data) {
    var postHtml = createPost(data.data);
    var post = $(postHtml);
    post.hide();
    var postsList = $('.posts');
    postsList.prepend(post);
    post.slideDown();
    linkifyText();
    submitFormAjax("#commentform_" + data.data.id, "displayComment", "ajaxProcessor.php?action=create&resource=comment");
}

/**
 * Display submitted data errors
 * 
 * @param {object} data submission result
 * @returns {undefined}
 */
function displayError(data) {
    var errorFieldIdentifier, errorField;
    var fieldNameExt = '';
    if (typeof data.data !== "undefined" && "postId" in data.data) {
        fieldNameExt = '_' + data.data.postId;
    }
    $.each(data.errors, function (fieldName, message) {
        if (message !== '') {
            var errorHtml = createError(message);
            var error = $(errorHtml);
            errorFieldIdentifier = '#' + fieldName + fieldNameExt;
            errorField = $(errorFieldIdentifier);
            errorField.after(error);
        }
    });
    errorField.focus();
}

/**
 * Display honeypot
 * 
 * @param {string} honeypotContainerSelector honeypot container
 * @param {int} postId
 * @returns {undefined}
 */
function displayHoneypot(honeypotContainerSelector, postId) {
    var honeypotHtml = createHoneypot(postId);
    var honeypot = $(honeypotHtml);
    var honeypotContainer = $(honeypotContainerSelector);
    honeypotContainer.prepend(honeypot);
}

/**
 * Get honeypot html
 * 
 * @param {int} postId
 * @returns {String} honeypot html
 */
function createHoneypot(postId) {
    var fieldNameExt = '';
    if ($.isNumeric(postId)) {
        fieldNameExt = '_' + postId;
    }
    var a = getRandom(5, 11);
    var b = getRandom(9, 20);
    var optionOne = '<input type="hidden" name="a' + fieldNameExt + '" value="' + a + '" id="a' + fieldNameExt + '" />' +
            '<input type="hidden" name="b' + fieldNameExt + '" value="' + b + '" id="b' + fieldNameExt + '" />' +
            '<label for="optionOne' + fieldNameExt + '" class="required">' + a + ' added to ' + b + ' equals </label>' +
            '<input type="text" name="optionOne' + fieldNameExt + '" id="optionOne' + fieldNameExt + '" class="form-control" value="" tabindex="1" required="required">';

    var honeypotMessages = [
        "I am not a human",
        "I never drink water",
        "I am not born on earth",
        "I am an alien",
        "Today is my 150th birthday",
        "A day is 48 hours",
        "I participated in first world war",
        "My laundry day is the 35th of each month",
        "I walk thousand miles to work everyday",
        "I spent 70 years in highschool",
        "I will be graduated in 2234",
        "I own the amazon not the website",
        "My pet cat is 0.1 mm long",
        "I speak hundered languages excluding english",
        "My car makes 2000 miles to the gallon",
        "When it is too hot, I switch the heater at full power",
        "An economy trip to the sun is pretty cheap",
        "I cook everyday using nuclear oven",
        "My room has all walls parallel to eachother",
        "I get a new phone, Once my current one has battery empty",
        "All currencies hold the same exact value",
        "Earth is a cube",
        "Choclate is necessary, To have high internet speed",
        "I travelled to jupiter planet"
    ];
    var honeypotMessage = honeypotMessages[Math.floor(Math.random() * honeypotMessages.length)];
    var optionTwo = '<input type="hidden" name="optionTwo' + fieldNameExt + '" value="0" />' +
            '<input type="checkbox" name="optionTwo' + fieldNameExt + '" value="1" /><span>' + honeypotMessage + '</span>';

    return optionOne + optionTwo;
}

/**
 * Get error HTML
 * 
 * @param {object} data submitted error
 * @returns {String} error HTML
 */
function createError(message) {
    var html = '' +
            '<div class="alert alert-danger">' +
            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
            message +
            '</div>';
    return html;
}

/**
 * Get comment HTML
 * 
 * @param {object} data submitted comment
 * @returns {String} comment HTML
 */
function createComment(data) {
    var html = '' +
            '<div class="panel panel-default">' +
            '<div class="panel-heading">' +
            '<strong>' + data.name + '</strong> <span class="text-muted">' + parseDisplayDate(data.created) + '</span>' +
            '</div>' +
            '<div class="panel-body">' +
            '<p class="notLinkified">' + data.message + '</p>' +
            '</div><!-- /panel-body -->' +
            '</div>';

    return html;
}

/**
 * Get post HTML
 * 
 * @param {object} data submitted post
 * @returns {String} post HTML
 */
function createPost(data) {
    var html = '<section id="content_' + data.id + '">' +
            '<div class="row">' +
            '<div class="col-sm-1">' +
            '<div class="thumbnail">' +
            '<img class="img-responsive user-photo" src="img/avatar.png">' +
            '</div><!-- /thumbnail -->' +
            '</div><!-- /col-sm-1 -->' +
            '<div class="col-sm-5">' +
            '<div class="panel panel-default">' +
            '<div class="panel-heading">' +
            '<strong><a href="mailto:' + data.email + '">' + data.name + '</a></strong> <span class="text-muted">' + parseDisplayDate(data.created) + '</span>' +
            '</div>' +
            '<div class="panel-body">' +
            '<p class="notLinkified">' + data.message + '</p>' +
            '</div><!-- /panel-body -->' +
            '<section class="col-sm-12" id="comments_' + data.id + '">' +
            '<ol id="posts-list_' + data.id + '" class="list-unstyled">' +
            '<li class="no-comments">Be the first to add a comment.</li>' +
            '</ol>' +
            '<div id="respond_' + data.id + '">' +
            '<h3>Leave a Comment</h3>' +
            '<form method="post" class="commentform" id="commentform_' + data.id + '">' +
            '<label for="name_' + data.id + '" class="required">Your name</label>' +
            '<input type="text" name="name_' + data.id + '" id="name_' + data.id + '" class="form-control" value="" tabindex="1" required="required">' +
            '<label for="message_' + data.id + '" class="required">Your message</label>' +
            '<textarea name="message_' + data.id + '" id="message_' + data.id + '" class="form-control" rows="2" tabindex="4"  required="required"></textarea>' +
            '<div class="antSugar_' + data.id + '">' + createHoneypot(data.id) + '</div>' +
            '<input type="hidden" name="post_id" value="' + data.id + '" />' +
            '<input name="submit_' + data.id + '" class="btn btn-primary" type="submit" value="Submit comment" />' +
            '</form>' +
            '</div>' +
            '</section>' +
            '</div><!-- /panel panel-default -->' +
            '</div><!-- /col-sm-5 -->' +
            '</div><!-- /row -->' +
            '</section>' +
            '<br>'
            ;
    return html;
}

/**
 * Get date string ready for display
 * 
 * @param {object} date
 * @returns {String} date string
 */
function parseDisplayDate(date) {
    date = (date instanceof Date ? date : new Date(Date.parse(date)));
    var display = date.getDate() + ' ' +
            ['January', 'February', 'March',
                'April', 'May', 'June', 'July',
                'August', 'September', 'October',
                'November', 'December'][date.getMonth()] + ' ' +
            date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes();
    return display;
}

/**
 * Linkify all paragraphs that are not linkified yet
 * 
 * @returns {undefined}
 */
function linkifyText() {
    $('p.notLinkified').linkify({
        target: "_blank"
    }).removeClass("notLinkified");
}

/**
 * Get random number
 * 
 * @param {Number} min
 * @param {Number} max
 * @returns {Number}
 */
function getRandom(min, max) {
    return min + Math.floor(Math.random() * (max - min + 1));
}