<template>
  <q-table
    title="Empresas"
    :rows="enterpriseData"
    :columns="columns"
    row-key="id"
    :loading="loading"
    loading-label="Carregando..."
    no-results-label="Nenhuma empresa encontrada"
    no-data-label="Nenhuma empresa encontrada"
    binary-state-sort
    @request="getEnterprisesFunction"
  >
    <template v-slot:top-right>
      <q-btn
        icon="add"
        label="Cadastrar"
        color="primary"
        outline
        :to="{ name: 'enterprise_create' }"
      />
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td key="actions" :props="props">
        <q-btn-group outline>
          <q-btn
            outline
            color="primary"
            icon="edit"
            :disable="removing === props.row.id"
            :to="{ name: 'enterprise_update', params: { 'id': props.row.id } }"
          >
            <q-tooltip>
              Editar
            </q-tooltip>
          </q-btn>
          <q-btn
            outline
            color="negative"
            icon="delete"
            :loading="removing === props.row.id"
            :disable="removing === props.row.id"
            @click="destroyEnterpriseFunction(props.row.id)"
          >
            <q-tooltip>
              Excluir
            </q-tooltip>
          </q-btn>
        </q-btn-group>
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { insertDocumentMask } from 'src/services/documents'
import { Notify, Dialog } from 'quasar'
import axios from "axios";

let enterpriseData = ref([])
let loading = ref(false)
let removing = ref(null)

const columns = [
  {
    name: 'name',
    label: 'Nome',
    align: 'left',
    field: 'name',
    format: val => val || 'N/I',
  },
  {
    name: 'document',
    label: 'CNPJ',
    align: 'left',
    field: 'document',
    format: val => insertDocumentMask(val) || 'N/I',
  },
  {
    name: 'phone',
    label: 'Telefone',
    align: 'left',
    field: 'phone',
    format: val => val || 'N/I',
  },
  {
    name: 'actions',
    align: 'center',
    label: 'Ações',
    field: 'id',
    sortable: false
  },
]

onMounted(async () => {
  await getEnterprisesFunction()
})

async function getEnterprisesFunction() {
  loading.value = true
  try {
    await axios
      .get(`http://localhost:8001/api/enterprise`)
      .then(( { data } ) => {
        enterpriseData.value = data
      })
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar empresas!',
      type: 'negative'
    })
  }
  loading.value = false
}

async function destroyEnterpriseFunction(enterprise) {
    Dialog.create({
      title: 'Atenção!',
      message: 'Tem certeza que deseja excluir esta empresa?',
      cancel: true,
    }).onOk(async () => {
      removing.value = enterprise
      console.log(enterprise)
      try {
        await axios
          .delete(`http://localhost:8001/api/enterprise/${enterprise}`)
        getEnterprisesFunction()

        Notify.create({
          message: 'Empresa excluída com sucesso!',
          type: 'positive'
        })
      } catch (e) {
        Notify.create({
          message: 'Falha ao excluir empresa!',
          type: 'negative'
        })
      }
      removing.value = null
    })
}
</script>
