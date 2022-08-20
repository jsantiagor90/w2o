<template>

    <q-card style="width: 800px; max-width: 90vw;">
      <div class="q-mt-md q-ml-md text-h6">
<!--        {{ !!treatmentRequestProduct.id ? 'Editar' : 'Adicionar' }} produto-->
      </div>


    </q-card>

</template>

<!--<template>-->
<!--  <q-btn-->
<!--    color="primary"-->
<!--    icon="arrow_back"-->
<!--    dense-->
<!--    outline-->
<!--    rounded-->
<!--    :to="{ name: 'enterprise' }"-->
<!--  >-->
<!--    <q-tooltip :offset="[5, 5]">-->
<!--      Voltar-->
<!--    </q-tooltip>-->
<!--  </q-btn>-->
<!--  <h4 class="q-mt-lg" v-if="!route.params.id">Criar empresa</h4>-->
<!--  <h4 class="q-mt-lg" v-else>Editar empresa</h4>-->
<!--  <q-form-->
<!--    ref="enterpriseForm"-->
<!--    @submit="submitEnterprise()"-->
<!--  >-->
<!--    <div>-->
<!--      <div class="row">-->
<!--        <div class="col-xs-12 col-sm-12 col-md-6 q-mr-md">-->
<!--          <q-input-->
<!--            label="Nome"-->
<!--            v-model="enterprise.name"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--            :rules="[val => !!val || 'Preenchimento obrigatório']"-->
<!--          />-->
<!--        </div>-->
<!--        <div class="col">-->
<!--          <q-input-->
<!--            label="CNPJ"-->
<!--            mask="##.###.###/####-##"-->
<!--            v-model="enterprise.document"-->
<!--            dense-->
<!--            outlined-->
<!--            :rules="[-->
<!--              checkIfCPForCNPJIsValid,-->
<!--              val => !!val || 'Preenchimento obrigatório'-->
<!--            ]"-->
<!--          />-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--    <div>-->
<!--      <div class="row">-->
<!--        <div class="col-xs-12 col-sm-12 col-md-6 col-py-xs q-mr-md q-mb-md">-->
<!--          <q-input-->
<!--            label="Telefone"-->
<!--            :mask="(enterprise.phone || '').length < 15-->
<!--              ? '(##) ####-#####' : '(##) #####-####'"-->
<!--            v-model="enterprise.phone"-->
<!--            dense-->
<!--            outlined-->
<!--            :rules="[val => !!val || 'Preenchimento obrigatório']"-->
<!--          />-->
<!--        </div>-->
<!--        <div class="col">-->
<!--          <q-input-->
<!--            label="E-mail"-->
<!--            v-model="enterprise.email"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--          />-->
<!--        </div>-->
<!--      </div>-->
<!--      <div class="row">-->
<!--        <div class="col-xs-12 col-sm-12 col-md-4 q-mr-md q-mb-lg">-->
<!--          <q-input-->
<!--            label="CEP"-->
<!--            mask="#####-###"-->
<!--            v-model="enterprise.zipcode"-->
<!--            dense-->
<!--            outlined-->
<!--            :loading="searchForZipCode"-->
<!--            @change="searchAddressWithZipcode()"-->
<!--          />-->
<!--        </div>-->
<!--        <div class="col-xs-12 col-sm-12 col-md-4 q-mr-md">-->
<!--          <q-input-->
<!--            label="Estado"-->
<!--            v-model="enterprise.state"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--            :rules="[val => (val || '').length <= 2 || 'Preencha o campo com a sigla do estado']"-->
<!--          />-->
<!--        </div>-->
<!--        <div class="col q-mb-lg">-->
<!--          <q-input-->
<!--            label="Cidade"-->
<!--            v-model="enterprise.city"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--          />-->
<!--        </div>-->
<!--      </div>-->
<!--      <div class="row">-->
<!--        <div class="col-xs-12 col-sm-12 col-md-4 q-mr-md q-mb-lg">-->
<!--          <q-input-->
<!--            label="Bairro"-->
<!--            v-model="enterprise.neighborhood"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--          />-->
<!--        </div>-->
<!--        <div class="col-xs-12 col-sm-12 col-md-4 q-mr-md q-mb-lg">-->
<!--          <q-input-->
<!--            label="Rua"-->
<!--            v-model="enterprise.street"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--          />-->
<!--        </div>-->
<!--        <div class="col q-mb-lg">-->
<!--          <q-input-->
<!--            label="Número"-->
<!--            v-model="enterprise.number"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--          />-->
<!--        </div>-->
<!--      </div>-->
<!--      <div class="row">-->
<!--        <div class="col-xs-12 col-sm-12 col-md-4 q-mr-md q-mb-lg">-->
<!--          <q-input-->
<!--            label="Complemento"-->
<!--            v-model="enterprise.complement"-->
<!--            dense-->
<!--            outlined-->
<!--            color="primary"-->
<!--          />-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--    <div align="right">-->
<!--      <q-btn-->
<!--        outline-->
<!--        label="Salvar"-->
<!--        icon="save"-->
<!--        type="submit"-->
<!--        color="primary"-->
<!--        :disable="saving"-->
<!--        :loading="saving"-->
<!--      />-->
<!--    </div>-->

<!--    <div v-if="enterprise.id">-->
<!--      <q-separator class="q-mt-lg"/>-->
<!--      <div class="q-mt-lg">-->
<!--        <service-order-products-list-component-->
<!--          ref="serviceOrderProductsListComponent"-->
<!--          :service-order="serviceOrder"-->
<!--        />-->
<!--      </div>-->
<!--    </div>-->
<!--  </q-form>-->
<!--</template>-->

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { onlyNumbers } from 'src/services/documents'
import { Notify, Loading } from 'quasar'
import axios from "axios";
import { useQuasar } from 'quasar'

const enterpriseForm = ref(null)
const router = useRouter()
const route = useRoute()
let saving = ref(false)
const enterprise = ref({})
const quasar = useQuasar()
let openModal = ref(false)

onMounted(async () => {
  await getEnterpriseFunction()
})

async function submitEnterprise() {
  saving.value = true
    const validated = enterpriseForm.value.validate()
    if (validated) {
      const enterpriseToSave = { ...enterprise.value }
      enterpriseToSave.document = onlyNumbers(enterpriseToSave.document)

      if(!route.params.id){
        await axios
          .post('http://localhost:8001/api/enterprise', enterpriseToSave)
          .then(( { data } ) => {
            const enterpriseToPush = data
            router.push({ name: 'enterprise_update', params: { 'id': enterpriseToPush.id } })
            Notify.create({
              message: 'Empresa criada com sucesso',
              type: 'positive'
            })
          }).catch (error => {
            Notify.create({
              message: 'Falha ao criar empresa',
              type: 'negative'
            })
          })
      }
      else {
        await axios
          .put(`http://localhost:8001/api/enterprise/${route.params.id}`, enterpriseToSave)
          .then(( { data } ) => {
            const enterpriseToPush = data
            router.push({ name: 'enterprise'})
            Notify.create({
              message: 'Empresa editada com sucesso',
              type: 'positive'
            })
          }).catch(error =>{
            Notify.create({
              message: 'Falha ao editar empresa',
              type: 'negative'
            })
          })
      }
    }
  saving.value = false
}

async function getEnterpriseFunction(enterpriseId) {
  Loading.show()
   await axios
     .get(`http://localhost:8001/api/enterprise/${enterpriseId}`)
     .then(( { data } ) => {
       enterprise.value = data
       console.log(enterprise.value, 'carregou sucesso')
     }).catch(error =>{
       console.log('carregou error')
     })
  Loading.hide()
}

</script>

<style scoped>

</style>
