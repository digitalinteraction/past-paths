INPUT=$1
SIZE=$2
if [ ! -d "${INPUT}" ]; then
    echo "invalid input directory '${INPUT}'" >&2
    exit 1
fi
# if [ ! -d "${SIZE}" ]; then
#     echo "invalid size input '${INPUT}' try x-small, medium, large" >&2
#     exit 1
# fi

mkdir -p ${INPUT}/small
mkdir -p ${INPUT}/medium
mkdir -p ${INPUT}/large

for FILENAME in $(find ${INPUT} -name "*.jpeg" -not -path "${INPUT}/medium")
do
	F=$FILENAME
	DIR=${F%/*}
	DELIM="/"
	# echo ${INPUT}/${DIR##*${DELIM}}



	mkdir -p ${INPUT}/${SIZE}/${DIR##*${DELIM}}

	# echo $NOEXT
	# echo ${SIZE}
	sips -Z 400  $FILENAME --out ${INPUT}/${SIZE}/${DIR##*${DELIM}}
	# sips -Z 100 $FILENAME --out ${INPUT}/${SIZE}/${DIR##*${DELIM}}
	# sips -Z 100 $FILENAME --out ${INPUT}/${SIZE}/${DIR##*${DELIM}}
	# sips -Z 100 $FILENAME --out ${INPUT}/${SIZE}/${DIR##*${DELIM}}
done

# recurse() {
#  for i in "$1"/*;do
#     if [ -d "$i" ];then
#         echo "dir: $i"
#         # echo "${INPUT}"
#         mkdir -p /$i
#         recurse "$i"
#     elif [ -f "$i" ]; then
#         echo "file: $i"
#     fi
#  done
# }

# recurse ${INPUT}