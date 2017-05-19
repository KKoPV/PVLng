Copy conf.js.dist and adjust for your needs

    cp config.js.dist config.js
    $EDITOR config.js

To use your own style, create a file `custom.css`

How about a black background...

    body {
        background-color: black;
    }

    #clock {
        color: #aaa;
    }
