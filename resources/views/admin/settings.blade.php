@extends ("admin/layouts/app")
@section ("title", "Settings")

@section ("main")

  <div class="pagetitle">
    <h1>Settings</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
        <li class="breadcrumb-item active">Settings</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">SMTP settings</h5>
            <form onsubmit="saveSMTPSettings()" id="form-smtp-setting">
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Host</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="host" />
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Port</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="port" />
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Encryption</label>
                <div class="col-sm-9">
                  <label>
                    SSL
                    <input type="radio" name="encryption" value="ssl" />
                  </label>
                  
                  <label>
                    TLS
                    <input type="radio" name="encryption" value="tls" />
                  </label>
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-9">
                  <input type="email" class="form-control" name="username" />
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" name="password" />
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">From email</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="from" />
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">From name</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="from_name" />
                </div>
              </div>

              <div class="row mb-3">
                <div class="offset-sm-3 col-sm-9">
                  <button type="submit" name="submit" class="btn btn-primary">Save settings</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    async function saveSMTPSettings() {
      event.preventDefault()
      const form = event.target
      const formData = new FormData(form)
      form.submit.setAttribute("disabled", "disabled")

      try {
        const response = await axios.post(
          baseUrl + "/api/admin/save-smtp-settings",
          formData,
          {
            headers: {
              Authorization: "Bearer " + localStorage.getItem(accessTokenKey)
            }
          }
        )

        if (response.data.status == "success") {
          swal.fire("Save settings", response.data.message, "success")
        } else {
          swal.fire("Error", response.data.message, "error")
        }
      } catch (exp) {
        swal.fire("Error", exp.message, "error")
      } finally {
        form.submit.removeAttribute("disabled")
      }
    }

    async function onInit() {
      try {
        const response = await axios.post(
          baseUrl + "/api/admin/fetch-smtp-settings",
          null,
          {
            headers: {
              Authorization: "Bearer " + localStorage.getItem(accessTokenKey)
            }
          }
        )

        if (response.data.status == "success") {
          const smtpSetting = response.data.smtp_setting
          const form = document.getElementById("form-smtp-setting")

          if (smtpSetting != null) {
            form.host.value = smtpSetting.host
            form.port.value = smtpSetting.port
            form.encryption.value = smtpSetting.encryption
            form.username.value = smtpSetting.username
            form.password.value = smtpSetting.password
            form.from.value = smtpSetting.from
            form.from_name.value = smtpSetting.from_name
          }
        } else {
          swal.fire("Error", response.data.message, "error")
        }
      } catch (exp) {
        swal.fire("Error", exp.message, "error")
      }
    }
  </script>

@endsection