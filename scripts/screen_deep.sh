if ! screen -list | grep -q "deepscreen"; then
    screen -d -m -S deepscreen bash -c './deep.sh'
else
    echo "deepscreen is active"
fi
