(function ( $ ) {
 
    $.fn.autoComplete = function(url, params={}, callback={}) {
    	this.after('<div class="auto-complete-wrapper"><ul></ul></div>');
    	this.on('keyup', function(event) {
			var context = this;
			var selector = $('~ .auto-complete-wrapper ul', context);
			dataParams = {'q':this.value};
			if(typeof params == 'object') dataParams = Object.assign(dataParams, params);
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: dataParams,
			}).always(function(response) {
				if (response.status==1) {
					var ul = document.createElement('ul');
					$.each(response.list, function(index, val) {
						var li = document.createElement('li');
						li.innerHTML = val;
						li.onclick = function(event){
							context.value = val;
							$('~ .auto-complete-wrapper ul>li', context).remove();
						}
						ul.append(li);
					});
					selector.replaceWith(ul);
				}

				if(typeof callback == 'function') callback.apply(context, [response]);
				if(typeof params == 'function') params.apply(context, [response]);
			});
		});
    }
 
}( jQuery ));