const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const axios = require('axios');

// Load .env
require('dotenv').config();

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
    cors: { origin: "*" }
});

// Start the server
server.listen(3002, () => {
    console.log("===> Socket.io Server Running on Port 3002 <===");
});

io.on('connection', (socket) => {
    console.log("===> Socket ID Connected: ", socket.id);
    console.log("===> Socket Connected: ", socket.connected);
    console.log("===> Socket Current Rooms: ", socket.rooms);
    console.log("===> Socket Handshake Query: ", socket.handshake.query);

    // Live
    socket.on("goLive", async (data) => {
        try {
            const dataOfgoLive = data;  // user_id, room_id
            console.log("===> goLive Data:  ", dataOfgoLive);

            const roomId = dataOfgoLive.room_id;
            socket.join(roomId); // Join room only on "goLive"
            console.log(`===> Socket ${socket.id} joined room ${roomId}`);

            // Add Live History & User
            const apiAddLiveHistory = `${process.env.APP_URL}/public/addlivehistory`;
            const response = await axios.post(apiAddLiveHistory, { user_id: dataOfgoLive.user_id, room_id: dataOfgoLive.room_id });
            console.log("===> goLive Response: ", response.data);

        } catch (error) {
            console.error('===> Error goLive: ', error.response ? error.response.data : error.message);
        }
    });
    socket.on("endLive", async (data) => {
        try {
            console.log("===> endLive Data:  ", data);

            // Update DB
            const apiEndLive = `${process.env.APP_URL}/public/endlive`;
            const response = await axios.post(apiEndLive, {
                user_id: data.user_id,
                room_id: data.room_id
            });
            console.log("===> endLive Response: ", response.data);

            // Notify all in room
            io.in(data.room_id).emit("roomDeleted", {
                room_id: data.room_id,
                is_close: true
            });

            // Kick everyone out of the room
            io.socketsLeave(data.room_id);

            console.log(`===> Room ${data.room_id} closed`);
        } catch (error) {
            console.error("===> Error endLive: ", error.response ? error.response.data : error.message);
        }
    });

    // View
    socket.on("addView", async (data) => {
        try {
            const dataOfaddView = data;  // { user_id, room_id }
            console.log("===> addView Data: ", dataOfaddView);

            // Validation
            if (!dataOfaddView.user_id || !dataOfaddView.room_id) {
                throw new Error("Invalid user_id or room_id in addView data");
            }

            // API : Add View
            const apiaddView = `${process.env.APP_URL}/public/addview`;
            const response = await axios.post(apiaddView, {
                user_id: dataOfaddView.user_id,
                room_id: dataOfaddView.room_id
            });
            console.log("===> addView Response: ", response.data);

            // Join the viewer into the live room
            const roomId = dataOfaddView.room_id;
            socket.join(roomId);
            console.log(`===> Socket ${socket.id} joined room ${roomId}`);

            // Updated viewer count to everyone in the room
            io.in(roomId).emit('addViewCountToClient', response.data.result.live_count);
        } catch (error) {
            console.error("===> Error addView: ", error.response ? error.response.data : error.message);
        }
    });
    socket.on("lessView", async (data) => {
        try {
            const dataOflessView = data;  // { user_id, room_id }
            console.log("===> lessView Data: ", dataOflessView);

            // Validation
            if (!dataOflessView.user_id || !dataOflessView.room_id) {
                throw new Error("Invalid user_id or room_id in addView data");
            }

            // API : Less View
            const apilessView = `${process.env.APP_URL}/public/lessview`;
            const response = await axios.post(apilessView, {
                user_id: dataOflessView.user_id,
                room_id: dataOflessView.room_id
            });
            console.log("===> lessView Response: ", response.data);

            // Remove the user from the room
            const roomId = dataOflessView.room_id;
            socket.leave(roomId);
            console.log(`===> Socket ${socket.id} left room ${roomId}`);

            // Updated viewer count to everyone in the room
            io.in(roomId).emit('addViewCountToClient', response.data.result.live_count);
        } catch (error) {
            console.error("===> Error lessView: ", error.response ? error.response.data : error.message);
        }
    });

    // Comment
    socket.on("liveChat", async (data) => {
        try {
            const dataOfliveChat = data;  // user_id, room_id, comment
            console.log("===> liveChat Data:  ", dataOfliveChat);

            // API : Live Chat
            const apiLiveChat = `${process.env.APP_URL}/public/livechat`;
            const response = await axios.post(apiLiveChat, {
                user_id: dataOfliveChat.user_id,
                room_id: dataOfliveChat.room_id,
                comment: dataOfliveChat.comment
            });
            console.log("===> liveChat Response: ", response.data);

            // Send chat to everyone in the room
            const roomId = dataOfliveChat.room_id;
            io.in(roomId).emit('liveChatToClient', response.data.result);
        } catch (error) {
            console.error("===> Error liveChat: ", error.response ? error.response.data : error.message);
        }
    });

    // Gift
    socket.on("sendGift", async (data) => {
        try {
            const dataOfsendGift = data;  // { user_id, room_id, gift_id }
            console.log("===> sendGift Data:  ", dataOfsendGift);

            // API : Send Gift
            const apiSendGift = `${process.env.APP_URL}/public/sendgift`;
            const response = await axios.post(apiSendGift, {
                user_id: dataOfsendGift.user_id,
                room_id: dataOfsendGift.room_id,
                gift_id: dataOfsendGift.gift_id
            });
            console.log("===> sendGift Response: ", response.data);

            // Broadcast to everyone in the room
            const roomId = dataOfsendGift.room_id;
            io.in(roomId).emit('sendGiftToClient', response.data.result);
        } catch (error) {
            console.error("===> Error sendGift: ", error.response ? error.response.data : error.message);
        }
    });

    // Disconnect
    socket.on('disconnect', async (reason) => {
        try {
            for (const roomId of socket.rooms) {
                if (roomId === socket.id) continue; // skip its own room

                socket.leave(roomId);

                const sockets = await io.in(roomId).fetchSockets();
                if (!sockets.length) {

                    io.to(roomId).emit("roomDeleted", { room_id: roomId, is_close: true });
                    rooms.delete(roomId);
                    console.log(`===> Room ${roomId} deleted (empty).`);
                }
            }
        } catch (error) {
            console.error("===> Disconnect cleanup error:", error.message);
        }
    });
});
