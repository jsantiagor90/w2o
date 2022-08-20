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
    <template v-slot:top-right>
      <q-btn
        icon="add"
        label="Adicionar colaborador"
        color="primary"
        outline
        @click="employeeFormFunction(false)"
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
            @click="employeeFormFunction(props.row.id)"
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
  <q-dialog
    v-model="employeesFormComponent"
  >
    <q-card style="width: 800px; max-width: 90vw;">
      <div class="q-mt-md q-ml-md text-h6 q-pa-sm">
        {{ !!employeeFormEdit ? 'Editar' : 'Adicionar' }} colaborador
      </div>
      <div class="">
      <q-form
        ref="employeeForm"
        @submit="submitEmployee()"
      >
        <div>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 q-mr-md q-pa-sm">
              <q-input
                label="Nome"
                v-model="employeeShow.name"
                dense
                outlined
                color="primary"
                :rules="[val => !!val || 'Preenchimento obrigatório']"
              />
            </div>
            <div class="col q-pa-sm">
              <q-input
                label="E-mail"
                v-model="employeeShow.email"
                dense
                outlined
                color="primary"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 q-mr-md q-pa-sm">
              <q-input
                label="Telefone"
                :mask="(employeeShow.phone || '').length < 15
              ? '(##) ####-#####' : '(##) #####-####'"
                v-model="employeeShow.phone"
                dense
                outlined
                :rules="[val => !!val || 'Preenchimento obrigatório']"
              />
            </div>
            <div class="col q-pa-sm">
              <q-input
                label="Data de nascimento"
                v-model="employeeShow.birth_date"
                mask="##/##/####"
                dense
                outlined
                lazy-rules
                clearable
                color="primary"
                :rules="[val => !val || parseDate(val) || 'Informe uma data válida']"
              >
                <template v-slot:append>
                  <q-icon name="event">
                    <q-popup-proxy
                      ref="returnForecast"
                      cover
                      transition-show="scale"
                      transition-hide="scale"
                    >
                      <q-date
                        label="Data de nascimento"
                        v-model="employeeShow.birth_date"
                        mask="DD/MM/YYYY"
                        @update:model-value="$refs.returnForecast.hide()"
                      >
                        <div class="row items-center justify-end">
                          <q-btn
                            label="OK"
                            color="primary"
                            flat
                            v-close-popup
                          />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
          </div>
        </div>
        <div
          class="col q-pa-md q-mt-md"
          align="right">
          <q-btn
            outline
            label="Salvar colaborador"
            icon="save"
            type="submit"
            color="primary"
            :disable="saving"
            :loading="saving"
          />
        </div>

        <div v-if="enterprise.id">
          <q-separator class="q-mt-lg"/>
          <div class="q-mt-lg">
            <employees-list-component
              :enterprise="enterprise.id"
            />
          </div>
        </div>
      </q-form>
      </div>
    </q-card>

  </q-dialog>

</template>

<script setup>
import { onMounted, ref } from 'vue'
import { parseDate, formatDateBR } from 'src/services/documents'
import { Notify, Dialog, Loading } from 'quasar'
import axios from "axios";

let employeeData = ref([])
let employeeShow = ref([])
let loading = ref(false)
let removing = ref(null)
const employeesFormComponent = ref(false)
let employeeFormEdit = ref(null)
let saving = ref(false)
const employeeForm = ref(null)

const props = defineProps({
  enterprise: {
    type: Object,
    required: true,
    default: ref({})
  }
})

function onSubmit() {
  getEmployeesFunction()
}

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
    format: val => formatDateBR(val) || 'N/I',
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

async function employeeFormFunction(employeeId) {
  employeesFormComponent.value = true
  employeeFormEdit.value = employeeId
  if (employeeId){
    await getEmployeeFunction(employeeId)
  } else {
    employeeShow.value = []
  }
}

async function getEmployeesFunction() {
  loading.value = true
  try {
    await axios
      .get(`http://localhost:8001/api/employee?enterprise_id=${props.enterprise}`)
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

async function getEmployeeFunction(employeeId) {
  Loading.show()
  await axios
    .get(`http://localhost:8001/api/employee/${employeeId}`)
    .then(( { data } ) => {
      console.log(data)
      employeeShow.value = data
    }) .catch(error =>{
      Notify.create({
        message: 'Falha ao carregar colaborador',
        type: 'negative'
      })
    })
  Loading.hide()
}

async function submitEmployee() {
  saving.value = true
  const validated = employeeForm.value.validate()
  if (validated) {
    const employeeToSave = { ...employeeShow.value }
    employeeToSave.enterprise_id = props.enterprise
    if(!employeeShow.value.id){
      await axios
        .post('http://localhost:8001/api/employee', employeeToSave)
        .then(( { data } ) => {
          const employeeToPush = data
          Notify.create({
            message: 'Empresa criada com sucesso',
            type: 'positive'
          })
          employeesFormComponent.value = false
          getEmployeesFunction()
        }).catch (error => {
          Notify.create({
            message: 'Falha ao criar empresa',
            type: 'negative'
          })
        })
    }
    else {
      await axios
        .put(`http://localhost:8001/api/employee/${employeeShow.value.id}`, employeeToSave)
        .then(( { data } ) => {
          Notify.create({
            message: 'Empresa editada com sucesso',
            type: 'positive'
          })
          employeesFormComponent.value = false
          getEmployeesFunction()
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

</script>
