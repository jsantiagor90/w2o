<template>
  <q-table
    title="Colaboradores"
    :rows="employeeData"
    :columns="columns"
    row-key="id"
    :loading="loading"
    loading-label="Carregando..."
    no-results-label="Nenhum empleado encontrado"
    no-data-label="Nenhum empleado encontrado"
    binary-state-sort
    @request="getEmployeesFunction"
  >
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
            @click="destroyEmployeesFunction(props.row.id)"
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

let employeeData = ref([])
let loading = ref(false)
let removing = ref(null)

const props = defineProps({
  enterprise: {
    type: Object,
    required: true,
    default: ref({})
  }
})

const columns = [
  {
    name: 'name',
    label: 'Nome',
    align: 'left',
    field: 'name',
    format: val => val || 'N/I',
  },
  {
    name: 'phone',
    label: 'Telefone',
    align: 'left',
    field: 'phone',
    format: val => val || 'N/I',
  },
  {
    name: 'email',
    label: 'E-mail',
    align: 'left',
    field: 'email',
    format: val => val || 'N/I',
  },
  {
    name: 'birth_date',
    label: 'Data de nascimento',
    align: 'left',
    field: 'birth_date',
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
  await getEmployeesFunction()
})

async function getEmployeesFunction() {
  loading.value = true
  try {
    await axios
      .get(`http://localhost:8001/api/employee`, {enterprise_id: props.enterprise})
      .then(( { data } ) => {
        employeeData.value = data
      })
  } catch (e) {
    Notify.create({
      message: 'Falha ao buscar colaborador!',
      type: 'negative'
    })
  }
  loading.value = false
}

async function destroyEmployeesFunction(employee) {
    Dialog.create({
      title: 'Atenção!',
      message: 'Tem certeza que deseja excluir este colaborador?',
      cancel: true,
    }).onOk(async () => {
      removing.value = employee
      console.log(employee)
      try {
        await axios
          .delete(`http://localhost:8001/api/employee/${employee}`)
        getEmployeesFunction()

        Notify.create({
          message: 'Colaborador excluída com sucesso!',
          type: 'positive'
        })
      } catch (e) {
        Notify.create({
          message: 'Falha ao excluir colaborador!',
          type: 'negative'
        })
      }
      removing.value = null
    })
}
</script>
