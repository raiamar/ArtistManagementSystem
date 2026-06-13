<div id="fof-main">
    <div class="fof text-center">
        <h1>Error 500</h1>
        <p class="text-2xl font-semibold text-gray-700 mt-4">Internal Server Error</p>
        <p class="text-gray-500 mt-2">Something went wrong. Please try again later.</p>
    </div>
</div>

<style>

    #fof-main {
        display: table;
        width: 100%;
        height: 100vh;
        text-align: center;
        font-family: 'Lato', sans-serif;
        color: #888;
        margin: 0;
    }

    .fof {
        display: table-cell;
        vertical-align: middle;
    }

    .fof h1 {
        font-size: 50px;
        display: inline-block;
        padding-right: 12px;
        animation: type .5s alternate infinite;
    }

    @keyframes type {
        from {
            box-shadow: inset -3px 0px 0px #888;
        }

        to {
            box-shadow: inset -3px 0px 0px transparent;
        }
    }
</style>