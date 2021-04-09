String.prototype.currency = function () {
	let decimal = Number(this).toFixed(2)
	return Intl.NumberFormat('pt-BR', {
		style: 'currency',
		currency: 'BRL'
	}).format(decimal)
}

Number.prototype.currency = function () {
	return this.toString().currency()
}

Number.prototype.pad = function (size) {
	var s = String(this)
	while (s.length < (size || 2)) { s = "0" + s }
	return s
}

import Vue from 'vue'
Vue.prototype.$today = () => {
	let date = new Date()
	return date.getFullYear() + "-" + Number(date.getMonth() + 1).pad(2) + "-" + Number(date.getDay()).pad(2) + "T00:00:00.000Z"
}

String.prototype.toSlug = function () {
	let str = this
	str = str.replace(/^\s+|\s+$/g, '')
	str = str.toLowerCase()
	let from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;"
	let to = "aaaaeeeeiiiioooouuuunc------"
	for (let i = 0, l = from.length;i < l;i++)  str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i))
	str = str.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-')
	return str != "undefined" ? str : ""
}
String.prototype.toClipboard = function (callback = () => { }) {
	const copy = require('copy-to-clipboard')
	copy(this)
	callback()
}

String.prototype.isValidDoc = function () {
	let value = this.replace(/[^0-9]/g, '')
	const validCpf = cpf => {
		if (!cpf || cpf.length != 11
			|| cpf == "00000000000"
			|| cpf == "11111111111"
			|| cpf == "22222222222"
			|| cpf == "33333333333"
			|| cpf == "44444444444"
			|| cpf == "55555555555"
			|| cpf == "66666666666"
			|| cpf == "77777777777"
			|| cpf == "88888888888"
			|| cpf == "99999999999")
			return false

		var soma = 0
		var resto
		for (var i = 1;i <= 9;i++)
			soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i)
		resto = (soma * 10) % 11
		if ((resto == 10) || (resto == 11)) resto = 0
		if (resto != parseInt(cpf.substring(9, 10))) return false
		soma = 0
		for (var i = 1;i <= 10;i++)
			soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i)
		resto = (soma * 10) % 11
		if ((resto == 10) || (resto == 11)) resto = 0
		if (resto != parseInt(cpf.substring(10, 11))) return false
		return true
	}

	const validCnpj = cnpj => {
		if (!cnpj || cnpj.length != 14
			|| cnpj == "00000000000000"
			|| cnpj == "11111111111111"
			|| cnpj == "22222222222222"
			|| cnpj == "33333333333333"
			|| cnpj == "44444444444444"
			|| cnpj == "55555555555555"
			|| cnpj == "66666666666666"
			|| cnpj == "77777777777777"
			|| cnpj == "88888888888888"
			|| cnpj == "99999999999999")
			return false
		var tamanho = cnpj.length - 2
		var numeros = cnpj.substring(0, tamanho)
		var digitos = cnpj.substring(tamanho)
		var soma = 0
		var pos = tamanho - 7
		for (var i = tamanho;i >= 1;i--) {
			soma += numeros.charAt(tamanho - i) * pos--
			if (pos < 2) pos = 9
		}
		var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
		if (resultado != digitos.charAt(0)) return false
		tamanho = tamanho + 1
		numeros = cnpj.substring(0, tamanho)
		soma = 0
		pos = tamanho - 7
		for (var i = tamanho;i >= 1;i--) {
			soma += numeros.charAt(tamanho - i) * pos--
			if (pos < 2) pos = 9
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
		if (resultado != digitos.charAt(1)) return false
		return true
	}
	return (value.length <= 11) ? validCpf(value) : validCnpj(value)
}

String.prototype.isValidCep = function () {
	const Trim = (strTexto) => {
		return strTexto.replace(/^s+|s+$/g, '')
	}

	const IsCEP = strCEP => {
		var objER = /^[0-9]{5}-[0-9]{3}$/
		strCEP = Trim(strCEP)
		if (strCEP.length > 0) {
			if (objER.test(strCEP))
				return true
			else
				return false
		}
		else return false
	}

	return IsCEP(this)
}

Vue.prototype.$validationErrorMessage = function (er) {
	er = er.response.data.errors
	let message = Object.keys(er).map(x => `<p class='mb-0'>${er[x]}</p>`).join("")
	this.$message({ dangerouslyUseHTMLString: true, showClose: true, message, type: "error" })
}

Vue.prototype.$validationErrorMessageDurantion = function (er, durationVal) {
	er = er.response.data.errors
	let message = Object.keys(er).map(x => `<p class='mb-0'>${er[x]}</p>`).join("")
	this.$message({ dangerouslyUseHTMLString: true, showClose: true, message, type: "error", duration: durationVal })
}

String.prototype.html = function () {
	return String(this).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
}

window.sleep = ms => {
	return new Promise(resolve => setTimeout(resolve, ms))
}

Vue.prototype.$insertParam = (key, value) => {
	if (history.pushState) {
		let searchParams = new URLSearchParams(window.location.search)
		searchParams.set(key, value)
		let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString()
		window.history.pushState({ path: newurl }, '', newurl)
	}
}

Vue.prototype.$percentage = (partialValue, totalValue, string = true) => {
	const value = `${+parseFloat((100 * Number(partialValue)) / Number(totalValue)).toFixed(2)}`
	return string ? `${value}%` : Number(value)
}

Vue.prototype.$debug = (content) => {
	if (laravel.config.debug) {
		console.log(content)
	}
}

Vue.prototype.$getEnabledIcons = function (enabled) {
	const icons = { true: '🟢', false: '🔴' }
	return icons[enabled ? 'true' : 'false']
}