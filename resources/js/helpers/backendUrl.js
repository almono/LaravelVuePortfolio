export function backendUrl() {

  //console.log(process.env)

  const app_status = process.env.MIX_FRONTEND_ENV !== undefined ? process.env.MIX_FRONTEND_ENV : 'production';
  const app_backend_port = process.env.VUE_APP_BACKEND_PORT !== undefined ? process.env.VUE_APP_BACKEND_PORT : '8000';

  let url = '';

  if( app_status === 'localhost')
  {
    url = 'http://localhost:' + app_backend_port + '/api'

  } else if( app_status === 'production' ) {
    url = 'http://website.almono.info/api'
  }

  return url
}