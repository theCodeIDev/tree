class Timer {
    constructor(qty = 18) {


        if (Timer.instance) {
            return Timer.instance;
        }

        this.count = this.qty = qty;
        this.countdownTimer = null;
        Timer.instance = this;
    }

    start(callback = () => {
    }) {
        this.countdownTimer = setInterval(() => {
            this.count--;
            console.log(this.count);
            callback(this.count);
            if (this.count === 1) {
                clearInterval(this.countdownTimer);
            }
        }, 1000);
    }

    stop() {
        clearInterval(this.countdownTimer);
        this.count = this.qty;
    }

}

