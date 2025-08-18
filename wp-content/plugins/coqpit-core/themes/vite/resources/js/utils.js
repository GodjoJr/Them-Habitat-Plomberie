export const documentReady = (fn) => {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

export const ajaxRequest = (url, nonce, action = null, data = {}) => {
    return new Promise((resolve, reject) => {

        let request = new XMLHttpRequest();

        request.open('POST', url, true);

        request.onload = function() {
            if (this.status >= 200 && this.status < 400){
                const data = JSON.parse(this.response)
                data.status = this.status
                resolve(data)
            } else if(this.status === 403){
                const data = JSON.parse(this.response)
                data.status = this.status
                reject(data)
            } else if(this.status === 422){
                const data = JSON.parse(this.response)
                data.status = this.status
                reject(data)
            } else {
                reject(new Error('An error occurred, data response has failed'))
            }
        }

        request.onerror = function() {
            reject(new Error('An error occurred, data response has failed'))
        }

        const formData = new FormData();

        for (const [key, value] of Object.entries(data)) {
            formData.append(key, value)
        }

        formData.append('action', action)
        formData.append('nonce', nonce)

        request.send(formData);

    })
}

export const debounce = (func, delay) => {
    let timer
    return (...args) => {
        clearTimeout(timer)
        timer = setTimeout(() => {
            func.apply(this, args)
        }, delay)
    }
}