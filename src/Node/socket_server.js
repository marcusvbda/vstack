require("dotenv").config({ path: "../../../../../.env" });

require("module-alias").addAliases({
    "~": __dirname,
    "@src": `${__dirname}/src`,
});
const express = require("express");

const app = express();
const http = require("http").createServer(app);

const SocketIo = require("socket.io");
const cors = require("cors");

app.use(cors());
app.use((req, res, next) => {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "X-Requested-With");
    res.header("Access-Control-Allow-Headers", "Content-Type");
    res.header("Access-Control-Allow-Methods", "PUT, GET, POST, DELETE, OPTIONS");
    next();
});

const io = SocketIo(http, {
    allowEIO3: true,
    cors: {
        origin: "*",
    },
});

io.sockets.on("connection", async (socket) => {
    // console.log("connected", { id: socket.id });
    socket.emit("connected", { id: socket.id });
});

const port = process.env.SOCKET_PORT_SERVER_PORT || 3003;
const route = process.env.APP_URL || "http://localhost";
http.listen(port, () => {
    console.log(`${route}:${port}`);
});

app.get("/", (req, res) => {
    res.json("socket server is running ...");
});
