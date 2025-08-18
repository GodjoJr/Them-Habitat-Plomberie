export const documentReady = (fn) => {
  if (document.readyState !== 'loading') {
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

export const wpRequest = (action = null, data = {}) => {
  return new Promise((resolve, reject) => {

    let request = new XMLHttpRequest();

    request.open('POST', coqpitCoreScriptAjax.ajaxUrl, true);

    request.onload = function() {
      if (this.status >= 200 && this.status < 400) resolve(JSON.parse(this.response))
      else reject(new Error('An error occurred, data response has failed'))
    }

    request.onerror = function() {
      reject(new Error('An error occurred, data response has failed'))
    }

    const formData = new FormData();

    for (const [key, value] of Object.entries(data)) {
      formData.append(key, value)
    }

    formData.append('action', action)
    formData.append('nonce', coqpitCoreScriptAjax.nonce)

    request.send(formData);

  })
}
