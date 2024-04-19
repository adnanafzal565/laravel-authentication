function Chat() {

    const [state, setState] = React.useState(globalState.state)
    const [message, setMessage] = React.useState("")
    const [sending, setSending] = React.useState(false)
    const [fetching, setFetching] = React.useState(false)
    const [messages, setMessages] = React.useState([])
    const [show, setShow] = React.useState(false)
    const [initialized, setInitialized] = React.useState(false)

    globalState.listen(function (newState) {
        setState(newState)
    })

    React.useEffect(function () {
        if (show && !initialized && state.user != null) {
            onInit()
        }
    }, [show])

    async function sendMessage() {
        setSending(true)
        const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone

        try {
            const formData = new FormData()
            formData.append("message", message)
            formData.append("time_zone", timeZone)

            const response = await axios.post(
                baseUrl + "/api/messages/send",
                formData,
                {
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem(accessTokenKey)
                    }
                }
            )

            if (response.data.status == "success") {
                const tempMessages = [...messages]
                const newMessage = response.data.message_obj
                tempMessages.push(newMessage)
                setMessages(tempMessages)
                setMessage("")
            } else {
                swal.fire("Error", response.data.message, "error")
            }
        } catch (exp) {
            swal.fire("Error", exp.message, "error")
        } finally {
            setSending(false)
        }
    }

    async function onInit() {
        setFetching(true)

        try {
            const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone
            const formData = new FormData()
            formData.append("time_zone", timeZone)

            const response = await axios.post(
                baseUrl + "/api/messages/fetch",
                formData,
                {
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem(accessTokenKey)
                    }
                }
            )

            if (response.data.status == "success") {
                const notificationsCount = response.data.notifications_count
                const newMessages = response.data.messages
                const tempMessages = [...messages]

                for (let a = newMessages.length - 1; a >= 0; a--) {
                    tempMessages.push(newMessages[a])
                }
                setMessages(tempMessages)
                setInitialized(true)

                let currentNotificationCount = document.getElementById("message-notification-badge").innerHTML ?? "0"
                currentNotificationCount = parseInt(currentNotificationCount)
                currentNotificationCount -= notificationsCount

                if (currentNotificationCount > 0) {
                  document.getElementById("message-notification-badge").innerHTML = currentNotificationCount
                } else {
                  document.getElementById("message-notification-badge").innerHTML = ""
                }
            } else {
                swal.fire("Error", response.data.message, "error")
            }
        } catch (exp) {
            swal.fire("Error", exp.message, "error")
        } finally {
            setFetching(false)
        }
    }

    return (
        <>
            { show && (
                <div className="page">
                  <div className="marvel-device nexus5">
                    <div className="top-bar"></div>
                    <div className="sleep"></div>
                    <div className="volume"></div>
                    <div className="camera"></div>
                    <div className="screen">
                      <div className="screen-container">
                        <div className="status-bar">
                          <div className="time"></div>
                          <div className="battery">
                            <i className="fa fa-battery"></i>
                          </div>
                          <div className="network">
                            <i className="fa fa-signal"></i>
                          </div>
                          <div className="wifi">
                            <i className="fa fa-wifi"></i>
                          </div>
                          {/*<div className="star">
                            <i className="fa fa-star"></i>
                          </div>*/}
                        </div>
                        <div className="chat">
                          <div className="chat-container">
                            <div className="user-bar">
                              {/*<div className="back">
                                <i className="fa fa-arrow-left"></i>
                              </div>*/}
                              <div className="avatar">
                                <img src="https://avatars.githubusercontent.com/u/12948048?v=4" alt="https://github.com/adnanafzal565" />
                              </div>
                              <div className="name">
                                <span>Admin</span>
                                {/*<span className="status">online</span>*/}
                              </div>
                              {/*<div className="actions more">
                                <i className="fa fa-more-vert"></i>
                              </div>
                              <div className="actions attachment">
                                <i className="fa fa-attachment"></i>
                              </div>
                              <div className="actions">
                                <i className="fa fa-phone"></i>
                              </div>*/}
                            </div>
                            <div className="conversation">
                              <div className="conversation-container">
                                {/*<div className="message sent">
                                  What happened last night?
                                  <span className="metadata">
                                      <span className="time"></span><span className="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"/></svg></span>
                                  </span>
                                </div>
                                <div className="message received">
                                  You were drunk.
                                  <span className="metadata"><span className="time"></span></span>
                                </div>*/}

                                { messages.map(function (m) {
                                    return (
                                        <div className={`message ${ m.sender_id == state.user.id ? "sent" : "received" }`} key={`message-${ m.id }`}>
                                          { m.message }
                                          <span className="metadata"><span className="time">{ m.created_at }</span></span>
                                        </div>
                                    )
                                }) }

                              </div>
                              <form className="conversation-compose">
                                {/*<div className="emoji">
                                    <i className="fa fa-paperclip"></i>
                                </div>*/}

                                <input className="input-msg" value={ message } onChange={ function () {
                                    setMessage(event.target.value)
                                } } name="input" placeholder="Type a message" autoComplete="off" autoFocus />
                                
                                {/*<div className="photo">
                                  <i className="fa fa-camera"></i>
                                </div>*/}
                                
                                <button className="send" type="button" onClick={ sendMessage } disabled={ sending }>
                                    <div className="circle" style={{
                                        backgroundColor: sending ? "gray" : "#008a7c"
                                    }}>
                                        <i className="fa fa-paper-plane"></i>
                                    </div>
                                </button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            ) }

            <button type="button" style={{
                position: "fixed",
                right: "20px",
                bottom: "20px",
                backgroundColor: "#4154f1",
                color: "white",
                border: "none",
                width: "60px",
                borderRadius: "50%",
                height: "60px"
            }} onClick={ function () {
                setShow(!show)
            } }>
                <span className="badge bg-danger" id="message-notification-badge"></span>
                Chat
            </button>
        </>
    )
}

ReactDOM.createRoot(
    document.getElementById("chat-app")
).render(<Chat />)