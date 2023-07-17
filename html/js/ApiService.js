class ApiService {
    constructor() {
        if (ApiService.instance) {
            return ApiService.instance;
        }
        ApiService.instance = this;
        this.url = './scripts/';
    }

    call(data, success = (data) => {
    }, fail = (data) => {
    }) {

        fetch(this.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((parcel) => {
                success(parcel);
            })
            .catch((error) => {
                console.error('Error:', error);
                fail(error);
            });
    }
}

apiImpl = new ApiService();


