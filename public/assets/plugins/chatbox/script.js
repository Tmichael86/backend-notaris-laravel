var custom_chat_box = {

	init : function(config={}) {

		var context = Object.assign(this, config);

		$('.chat-wrapper .chat-open').click(function (e) {
			e.preventDefault();
			this.closest('.chat-wrapper').setAttribute('window', 'open');
		});

		$('.chat-wrapper .chat-close').click(function (e) {
			e.preventDefault();
			this.closest('.chat-wrapper').setAttribute('window', 'close');
		});

		$('.chat-wrapper form').submit(function(event) {
			event.preventDefault();
			var serialize = $(this).serializeArray();
			var formdata = serialize.concat(context.formdata);
			$.LoadingOverlay("show");

			var ajaxConfig = {
	            url: context.url,
	            type: 'post',
	            data: formdata,
	            dataType: 'json',
	        };

			$.ajax(ajaxConfig).always(function(response){
				$.LoadingOverlay("hide");

				if(response.status==1) {
					$('.chat-wrapper textarea').val('');
					$('.chat-wrapper').attr('window', 'close');
				} else {
					swal('Maaf!', 'Mohon periksa koneksi internet anda.', 'error');
				}

				if(typeof context.callback == 'function') context.callback.apply(context, [response]);
			});
		});

		return this;

	},

	url_set : function(url) {
		this.url = url;
		return this;
	},

	formdata_set : function(set) {
		this.formdata = set;
		return this;
	},

	callback_get : function(callback) {
		this.callback = callback;
		return this;
	}

}