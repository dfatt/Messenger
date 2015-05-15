$(function () {

	var route = {
		new_message: '/messenger/new-message',
		delete_message: '/messenger/delete-message',
		new_like: '/messenger/new-like'
	};

	var methods = {
		new_like: 'new_like',
		new_message: 'new_message',
		delete_message: 'delete_message'
	};

	var csrfToken = $('meta[name="csrf-token"]').attr("content");
	var user = $('#user').val();
	var attaches = [];

	var conn = new WebSocket('ws://localhost:8080');

	/**
	 *
	 * @param url
	 * @param data
	 * @param error
	 * @param done
	 */
	var ajaxRequest = function (url, data, error, done) {
		$.ajax({
			url: url,
			method: 'POST',
			data: data,
			dataType: 'json'
		}).error(function (d) {
			error(d)
		}).done(function (d) {
			done(d);
		});
	};

	/**
	 * Результат открытия сокета
	 * @param result
	 */
	conn.onopen = function (result) {
		console.log(result);
	};

	/**
	 * Рассылка сообщений подписчикам
	 * @param result
	 */
	conn.onmessage = function (result) {
		var json = JSON.parse(result.data);
		var message_id = null;
		var content = null;
		var user_name = null;
		var created_at = null;

		if (json !== null) {
			switch (json['type']) {
				case methods.new_message:
					message_id = json['message_id'];
					content = json['content'];
					user_name = json['user_name'];
					created_at = json['created_at'];

					add_message(message_id, content, user_name, created_at);

					break;

				case methods.delete_message:
					message_id = json['message_id'];
					delete_message(message_id);

					break;

				case methods.new_like:
					var count = json['count'];
					message_id = json['message_id'];
					content = json['content'];
					user_name = json['user_name'];

					add_like(message_id, count);
					break;
			}
		}
	};

	/**
	 * Отправка сообщения в чат
	 */
	$('#send-message').on('click', function () {
		var input = $('#message-text');
		var text = input.val();
		var content = '';
		if (text.length > 0) {
			var attach_to_string = '';
			$.each(attaches, function (key, value) {
				switch (value.type) {
					case 'image':
						attach_to_string += '<img src="/storage/' + value.content + '">';
						break;
					case 'video':
						attach_to_string += '<iframe src="https://www.youtube.com/embed/' + value.content + '" frameborder="0" allowfullscreen></iframe>';
						break;
					case 'link':
						attach_to_string += '<p><a href="' + value.content + '">Ссылка</a></p>';
						break;
				}
			});

			content = text + attach_to_string;

			ajaxRequest(
				route.new_message,
				{_csrf: csrfToken, content: content, user_name: user},
				function (error) {
					console.log(error);
				},
				function (message) {
					conn.send(JSON.stringify({
						type: methods.new_message,
						content: content,
						user_name: user,
						message_id: message.id,
						created_at: message.created_at
					}));

					input.val('');

					add_message(message.id, text, user, message.created_at);
				}
			);
		}
	});

	/**
	 * Удаление сообщения из чата
	 */
	$('.messages').on('click', '#delete', function () {
		var message = $(this).parent('.message');
		var message_id = message.attr('id');

		ajaxRequest(
			route.delete_message,
			{_csrf: csrfToken, message_id: message_id},
			function (error) {
				console.log(error);
			},
			function () {
				conn.send(JSON.stringify({
					type: methods.delete_message,
					message_id: message_id
				}));

				delete_message(message_id);
			}
		);

		return false;
	});

	/**
	 * Добавление лайка сообщению
	 */
	$('.messages').on('click', '.like', function () {
		var message_id = $(this).parent('.message').attr('id');

		ajaxRequest(
			route.new_like,
			{_csrf: csrfToken, message_id: message_id, user_name: user},
			function (error) {
				console.log(error);
			},
			function (count) {
				conn.send(JSON.stringify({
					type: methods.new_like,
					message_id: message_id,
					user_name: user,
					count: count
				}));

				add_like(message_id, count);
			}
		);

		return false;
	});


	/**
	 *
	 * @param id
	 * @param content
	 * @param user_name
	 * @returns {string}
	 * @param created_at
	 */
	function add_message(id, content, user_name, created_at) {
		var attach_to_string = '';
		$.each(attaches, function (key, value) {
			switch (value.type) {
				case 'image':
					attach_to_string += '<img src="/storage/' + value.content + '">';
					break;
				case 'video':
					attach_to_string += '<iframe src="https://www.youtube.com/embed/' + value.content + '" frameborder="0" allowfullscreen></iframe>';
					break;
				case 'link':
					attach_to_string += '<p><a href="' + value.content + '">Ссылка</a></p>';
					break;
			}
		});

		var delete_link = user === user_name ? '<a href="#" id="delete">Удалить</a>' : '';
		var msg = $('<div class="message" id="' + id + '"><h4>' + user_name + ' says:</h4>' + content + attach_to_string + '<br><br><small>' + created_at + '</small><a class="like" href="#">Like <span></span></a><br>' + delete_link + '</div>');

		$('.messages').append(msg);
		msg.hide().fadeIn(500);

		attaches = [];
		$('#attaches').text('');
	}

	function delete_message(message_id) {
		var message = $('#' + message_id);

		message.slideUp();
	}

	/**
	 *
	 * @param message_id
	 * @param count
	 */
	function add_like(message_id, count) {
		var message = $('#' + message_id);
		var like_element = message.find('.like span');

		like_element.hide().fadeIn(500).text('').text(' ' + count);

		console.log(message);
	}


	/**
	 * Вызов диалога для загрузки изображений
	 */
	$('.add-pictures-link').on('click', function () {
		$('.add-pictures-dialog').fadeIn();
		$('.attach-menu').fadeOut();
		return false;
	});

	/**
	 * Вызов диалога для добавления видео
	 */
	$('.add-video-link').on('click', function () {
		$('.add-video-dialog').fadeIn();
		return false;
	});

	$('.add-url-link').on('click', function () {
		$('.add-url-dialog').fadeIn();
		return false;
	});

	/**
	 * Закрытие диалогового окна
	 */
	$('.close-dialog').on('click', function () {
		$('.dialog').fadeOut();
		return false;
	});

	/**
	 *
	 */
	$('.add-video').on('click', function () {
		var video_url = $('#video-url').val();
		var youtube_id = youTubeGetId(video_url);

		if (youtube_id) {
			attaches.push({
				type: 'video',
				content: youtube_id
			});

			$('#video-form').hide();
			$('#success_video').hide().fadeIn().text('Видео было добавлено к сообщению');
		} else {
			alert('Убедитесь в правильности ссылки на видео');
		}

		return false;
	});

	$('.add-url').on('click', function () {
		var url = $('#url').val();

		if (validationUrl(url)) {
			attaches.push({
				type: 'link',
				content: url
			});

			$('#url-form').hide();
			$('#success_url').hide().fadeIn().text('Ссылка добавлена к сообщению');
		} else {
			alert('Убедитесь в правильности ссылки');
		}

		return false;
	});

	/**
	 * Прослушивание события fileuploaded,
	 * для определения какие файлы были загружены
	 */
	$('input[name=\'UploadForm[file]\']').on('fileuploaded', function (event, data) {
		var response = data.response;

		attaches.push({
			type: 'image',
			content: response
		});

		$('#attaches').append('<img src="/storage/' + response + '">');
	});

	/**
	 *
	 * @param url
	 * @returns {*|Array|{index: number, input: string}}
	 */
	function youTubeGetId(url) {
		var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
		var video = (url.match(p)) ? RegExp.$1 : false;

		if (video) {
			return video;
		} else {
			return false;
		}
	}

	function validationUrl(url) {
		var p = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
		var link = (url.match(p)) ? RegExp.$1 : false;

		if (link) {
			return link;
		} else {
			return false;
		}
	}
});