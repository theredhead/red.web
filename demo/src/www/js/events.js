window.triggerEvent = function (sender, triggerName, triggerArgument) {
	var senderId, evInput, argInput, form;

	do {form = (form == null) ? form = sender : form.parentNode;} while (form != null && form.localName != 'form');

	if (form) {
		senderId = sender.getAttribute('id');
		
		evInput = form.appendChild(document.createElement('input'));
		evInput.setAttribute('type', 'hidden');
		evInput.setAttribute('name', senderId + '_ev');
		evInput.setAttribute('value', triggerName);

		if (typeof triggerArgument != 'undefined') {
			argInput = form.appendChild(document.createElement('input'));
			argInput.setAttribute('type', 'hidden');
			argInput.setAttribute('name', senderId + '_arg');
			argInput.setAttribute('value', triggerArgument);
		}
		form.submit();
	}
}