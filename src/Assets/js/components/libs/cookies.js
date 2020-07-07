const Cookies = function () {
    this.set = (index, value, time = 999999) => {
        this.remove(index)
        let text = `${index}=${String(value)}`
        var date = new Date()
        date.setTime(+ date + (time * 86400000))
        text += `;expires=${date.toGMTString()};path=/;`
        window.document.cookie = text
    }
    this.get = (index) => {
        let parsed = this.getParsedCookie()
        return parsed[index] ? parsed[index] : null
    }
    this.getParsedCookie = () => {
        let cookie = window.document.cookie
        let parsed = cookie.split(";")
        let result = {}
        for (let i in parsed) {
            if (parsed[i].split) {
                let aux = parsed[i].split("=")
                result[aux[0] ? aux[0].trim() : ""] = aux[1] ? aux[1].trim() : ""
            }
        }
        return result
    }
    this.remove = (index) => {
        let text = `${index}=`
        var date = new Date()
        date.setTime(+ date + (-1 * 86400000))
        text += `;expires=${date.toGMTString()};path=/;`
        window.document.cookie = text
    }
}
window.Cookies = new Cookies()
