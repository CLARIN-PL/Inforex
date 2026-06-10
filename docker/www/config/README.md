A default configuration file used inside docker will be copied here.

The Docker setup now enables local OIDC authentication against the bundled Keycloak container.

Current defaults:
* public issuer: `http://localhost:9081/realms/inforex`
* internal Keycloak base url: `http://keycloak:8080`
* client id: `inforex-local`
* redirect uri: `http://localhost:9080/inforex/index.php?page=oidc_callback`

The browser uses the public issuer on `localhost:9081`, while the PHP container talks to Keycloak over the Docker network using `http://keycloak:8080`.

For Keycloak 26.x, `KC_HOSTNAME` must be a plain hostname or a full URL. In this Docker setup it should be `http://localhost:9081`.
