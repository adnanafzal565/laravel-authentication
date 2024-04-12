function Header() {

    const [state, setState] = React.useState(globalState.state)

    globalState.listen(function (newState) {
        setState(newState)
    })

    async function onInit() {
        const accessToken = localStorage.getItem(accessTokenKey)
        if (accessToken) {
            try {
                const response = await axios.post(
                    baseUrl + "/api/me",
                    null,
                    {
                        headers: {
                            Authorization: "Bearer " + accessToken
                        }
                    }
                )

                if (response.data.status == "success") {
                    globalState.setState({
                        user: response.data.user
                    })

                    window.onInit()
                } else {
                    // swal.fire("Error", response.data.message, "error")
                }
            } catch (exp) {
                // swal.fire("Error", exp.message, "error")
            }
        } else {
            window.location.href = baseUrl + "/admin/login"
        }
    }

    React.useEffect(function () {
        onInit()
    }, [])

    async function logout() {
        try {
            const response = await axios.post(
                baseUrl + "/api/logout",
                null,
                {
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem(accessTokenKey)
                    }
                }
            )

            if (response.data.status == "success") {
                globalState.setState({
                    user: null
                })
                localStorage.removeItem(accessTokenKey)
                window.location.href = baseUrl + "/admin/login"
            } else {
                swal.fire("Error", response.data.message, "error")
            }
        } catch (exp) {
            swal.fire("Error", exp.message, "error")
        }
    }

    return (
        <>
            <div className="d-flex align-items-center justify-content-between">
              <a href={`${ baseUrl }/admin`} className="logo d-flex align-items-center">
                <img src={`${ baseUrl }/administrator/img/logo.png`} alt="" />
                <span className="d-none d-lg-block">Admin panel</span>
              </a>
              <i className="bi bi-list toggle-sidebar-btn"></i>
            </div>

            <div className="search-bar">
              <form className="search-form d-flex align-items-center" method="POST" action="#">
                <input type="text" name="query" placeholder="Search" title="Enter search keyword" />
                <button type="submit" title="Search"><i className="bi bi-search"></i></button>
              </form>
            </div>

            <nav className="header-nav ms-auto">
              <ul className="d-flex align-items-center">

                <li className="nav-item d-block d-lg-none">
                  <a className="nav-link nav-icon search-bar-toggle " href="#">
                    <i className="bi bi-search"></i>
                  </a>
                </li>

                <li className="nav-item dropdown pe-3">

                  <a className="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src={`${ baseUrl }/administrator/img/profile-img.jpg`} alt="Profile" className="rounded-circle" />
                    <span className="d-none d-md-block dropdown-toggle ps-2">{ state.user?.name }</span>
                  </a>

                  <ul className="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li className="dropdown-header">
                      <h6>{ state.user?.name }</h6>
                      <span>{ state.user?.email }</span>
                    </li>

                    <li>
                      <hr className="dropdown-divider" />
                    </li>

                    <li>
                      <a className="dropdown-item d-flex align-items-center" href="javascript:void(0)" onClick={ logout }>
                        <i className="bi bi-box-arrow-right"></i>
                        <span>Sign Out</span>
                      </a>
                    </li>

                  </ul>
                </li>

              </ul>
            </nav>
        </>
    )
}

ReactDOM.createRoot(
    document.getElementById("header-app")
).render(<Header />)