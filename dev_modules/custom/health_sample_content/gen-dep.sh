for f in config/install/*.yml ; do
  name=$f|sed 's/\$.yml$//g'
  echo $name
done
