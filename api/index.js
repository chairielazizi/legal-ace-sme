const php = require("@vercel/php");

module.exports = async (req, res) => {
    const indexPath = "public/index.php";
    const phpResponse = await php(indexPath);
    res.set("Content-Type", "text/html");
    res.send(phpResponse);
};
