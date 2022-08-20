import axios from "axios";

export const insertDocumentMask = (doc) => {
  if (!doc) {
    return doc
  }

  doc = doc.replace(/\D/g, "")

  if (doc.length <= 11) {
    doc = doc.replace(/(\d{3})(\d)/, "$1.$2")
    doc = doc.replace(/(\d{3})(\d)/, "$1.$2")
    doc = doc.replace(/(\d{3})(\d{1,2})$/, "$1-$2")
  } else {
    doc = doc.replace(/^(\d{2})(\d)/, "$1.$2")
    doc = doc.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
    doc = doc.replace(/\.(\d{3})(\d)/, ".$1/$2")
    doc = doc.replace(/(\d{4})(\d)/, "$1-$2")
  }

  return doc
}

export const validateCPForCNPJ = (val) => {
  if (val.length == 14) {
    var cpf = val.trim();

    cpf = cpf.replace(/\./g, '');
    cpf = cpf.replace('-', '');
    cpf = cpf.split('');

    var v1 = 0;
    var v2 = 0;
    var aux = false;

    for (var i = 1; cpf.length > i; i++) {
      if (cpf[i - 1] != cpf[i]) {
        aux = true;
      }
    }

    if (aux == false) {
      return false;
    }

    for (var i = 0, p = 10; (cpf.length - 2) > i; i++, p--) {
      v1 += cpf[i] * p;
    }

    v1 = ((v1 * 10) % 11);

    if (v1 == 10) {
      v1 = 0;
    }

    if (v1 != cpf[9]) {
      return false;
    }

    for (var i = 0, p = 11; (cpf.length - 1) > i; i++, p--) {
      v2 += cpf[i] * p;
    }

    v2 = ((v2 * 10) % 11);

    if (v2 == 10) {
      v2 = 0;
    }

    if (v2 != cpf[10]) {
      return false;
    } else {
      return true;
    }
  } else if (val.length == 18) {
    var cnpj = val.trim();

    cnpj = cnpj.replace(/\./g, '');
    cnpj = cnpj.replace('-', '');
    cnpj = cnpj.replace('/', '');
    cnpj = cnpj.split('');

    var v1 = 0;
    var v2 = 0;
    var aux = false;

    for (var i = 1; cnpj.length > i; i++) {
      if (cnpj[i - 1] != cnpj[i]) {
        aux = true;
      }
    }

    if (aux == false) {
      return false;
    }

    for (var i = 0, p1 = 5, p2 = 13; (cnpj.length - 2) > i; i++, p1--, p2--) {
      if (p1 >= 2) {
        v1 += cnpj[i] * p1;
      } else {
        v1 += cnpj[i] * p2;
      }
    }

    v1 = (v1 % 11);

    if (v1 < 2) {
      v1 = 0;
    } else {
      v1 = (11 - v1);
    }

    if (v1 != cnpj[12]) {
      return false;
    }

    for (var i = 0, p1 = 6, p2 = 14; (cnpj.length - 1) > i; i++, p1--, p2--) {
      if (p1 >= 2) {
        v2 += cnpj[i] * p1;
      } else {
        v2 += cnpj[i] * p2;
      }
    }

    v2 = (v2 % 11);

    if (v2 < 2) {
      v2 = 0;
    } else {
      v2 = (11 - v2);
    }

    if (v2 != cnpj[13]) {
      return false;
    } else {
      return true;
    }
  } else {
    return false;
  }
}

export const locationFromZipCode = async (zipCode) => {
  const axiosClient = axios.create()
  delete axiosClient.defaults.headers.common.Authorization

  try {
    const { data } = await axiosClient.get(`https://viacep.com.br/ws/${zipCode}/json/`)

    if ('erro' in data) {
      throw new Error('CEP não encontrado')
    }

    return {
      zipcode: data.cep,
      city: data.localidade,
      state: data.uf,
      street: data.logradouro,
      neighborhood: data.bairro,
      complement: data.complemento,
      areacode: data.ddd,
      gia: data.gia, // ICMS (somente SP)
      ibge: data.ibge,
      siafi: data.siafi
    }
  } catch (e) {
    throw new Error(e)
  }
}

export const onlyNumbers = (val) => {
  return val.replace(/[^0-9]/g, "")
}

export const parseDate = datetime => {
  if (datetime.includes('/')) {
    const splitedDatetime = datetime.split(' ')

    const splitedDate = splitedDatetime[0].split('/')

    splitedDatetime[1] = splitedDatetime[1] ? ` ${splitedDatetime[1]}` : ''
    return Date.parse(`${splitedDate[2]}-${splitedDate[1]}-${splitedDate[0]}${splitedDatetime[1]}`)
  }

  return Date.parse(datetime)
}

export const formatDateBR = (datetime) => {
  if (!datetime) {
    return datetime
  }

  if (datetime.includes('/')) {
    return datetime
  }

  const splitedDatetime = datetime.split(' ')

  const date = splitedDatetime[0].split('-').reverse().join('/')

  const time = splitedDatetime[1] ? ` ${splitedDatetime[1]}` : ''
  return `${date}${time}`
}
