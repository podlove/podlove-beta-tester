build:
	rm -rf dist
	mkdir dist
	# move everything into dist
	rsync -r --exclude=.git --exclude=dist . dist
	# cleanup
	rm dist/.gitignore
	rm dist/Makefile
