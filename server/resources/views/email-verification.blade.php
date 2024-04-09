@extends ("layouts/app")
@section ("title", "Email verification")

@section ("main")

    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <div class="row">
            <div class="offset-4 col-4">
                <h2>Email verification</h2>

                <form onsubmit="verifyEmail()">
                    <input type="hidden" name="email" value="{{ $email }}" />

                    <div class="form-group" style="margin-top: 15px; margin-bottom: 15px;">
                        <label class="form-label">Enter code</label>
                        <input type="text" name="code" class="form-control" required />
                    </div>

                    <input type="submit" name="submit" class="btn btn-outline-primary btn-sm" value="Verify" />
                </form>
            </div>
        </div>
    </div>

    <script>
        async function verifyEmail() {
            event.preventDefault()
            const form = event.target

            try {
                const formData = new FormData(form)
                form.submit.setAttribute("disabled", "disabled")

                const response = await axios.post(
                    baseUrl + "/api/verify-email",
                    formData
                )

                if (response.data.status == "success") {
                    swal.fire("Verify email", response.data.message, "success")
                        .then(function () {
                            window.location.href = baseUrl + "/login"
                        })
                } else {
                    swal.fire("Error", response.data.message, "error")
                }
            } catch (exp) {
                swal.fire("Error", exp.message, "error")
            } finally {
                form.submit.removeAttribute("disabled")
            }
        }
    </script>

@endsection