require("dotenv").config({ path: "./.env" });
const origin = process.env.APP_URL || "http://localhost";
const express = require("express");
const SocketIo = require("socket.io");
const http = require("http");
const app = express();
const bodyParser = require("body-parser");
const cors = require("cors");
app.use(cors());

server = http.createServer(app);
const io = SocketIo(server, {
    allowEIO3: true,
    cors: {
        origin: origin,
    },
});

const clients = {};

const port = process.env.SOCKET_PORT_SERVER_PORT || 3003;
server.listen(port, () => {
    console.log(`${origin}:${port}`);
});

app.use(bodyParser.json());

app.get("/", (req, res) => {
    res.send("Socket server is running ...");
});

app.post("/dispatch-event/:id", (req, res) => {
    const event = req.body.event;
    if (!event) {
        return res.status(404).send("Event not found");
    }

    const data = req.body.data || {};

    const type = req.body.index || "client";
    const id = req.params.id;

    if (type == "room") {
        io.to(id).emit(event, data);
    } else if (type == "client") {
        const client = clients[id];
        if (!client) {
            return res.status(404).send("Client not found");
        } else {
            client.emit(event, data);
        }
    }

    return res.json({ id, type, event, data });
});

io.on("connection", (client) => {
    clients[client.id] = client;

    client.emit("connected", { id: client.id });

    client.on("join", (room) => {
        client.join(room);

        client.emit("joined", { room });
    });

    client.on("disconnect", () => {
        clients[client.id] && delete clients[client.id];
    });
});
