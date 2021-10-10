export function backendUrl() {

  //console.log(process.env)

  const app_status = process.env.VUE_APP_STATUS !== undefined ? process.env.VUE_APP_STATUS : 'localhost'
  const app_backend_port = process.env.VUE_APP_BACKEND_PORT !== undefined ? process.env.VUE_APP_BACKEND_PORT : '8000'

  let url = ''

  if( app_status == 'localhost')
  {
    url = 'http://localhost:' + app_backend_port + '/api'
  } else {
    url = 'http://localhost:' + app_backend_port + '/api'
  }

  return url
}