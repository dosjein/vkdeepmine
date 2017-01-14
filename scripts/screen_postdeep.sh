if ! screen -list | grep -q "postdeepscreen"; then
    screen -d -m -S postdeepscreen bash -c './postdeep.sh'
else
    echo "postdeepscreen is active"
fi
