const fs = require('fs')
const path = require('path')

const excludeDirs = ['fonts', 'mixins', 'tools']
const bootstrapFile = path.join(__dirname, '../resources/scss/files.scss')
const files = getAllScssFiles(__dirname + '/../resources/scss')

const formattedPaths = files.map(file => {
    const relativePath = path.relative(path.join(__dirname, '../resources/scss'), file)
    const forwardPath = relativePath.replace(/\\/g, '/')

    if (!excludeDirs.includes(forwardPath.split('/')[0])) {
        const namespace = getNamespace(forwardPath)
        return `@use '${forwardPath}' as ${namespace};`
    }
    return null
}).filter(Boolean).join('\n')

fs.writeFile(bootstrapFile, formattedPaths, (err) => {
    if (err) throw err
})

function getAllScssFiles(dir) {
    let results = []
    let excludeFiles = ['example-plugin.scss', 'files.scss', 'globals.scss']
    const list = fs.readdirSync(dir)

    
    list.forEach(file => {
        const filePath = path.join(dir, file)
        const stat = fs.statSync(filePath)

        if (stat && stat.isDirectory()) {
            results = results.concat(getAllScssFiles(filePath)) // If folder, recursive call
        } else if (file.endsWith('.scss') && !excludeFiles.includes(file)) {
            results.push(filePath) // If .scss file, add to results[]
        }
    })

    return results
}

function getNamespace(forwardPath) {
    const cleanPath = forwardPath.replace(/\.scss$/, '') // Remove .scss extension
    const parts = cleanPath.split('/')

    // Convert each part to PascalCase
    const namespaceParts = parts.map(part => {
        return toPascalCase(part)
    })

    return namespaceParts.join('')
}

function toPascalCase(str) {
    return str
        .replace(/^_/, '') // Remove leading underscore
        .replace(/[-_](.)/g, (_, letter) => letter.toUpperCase()) // Convert - or _ followed by a letter to uppercase
        .replace(/^[a-z]/, letter => letter.toUpperCase()) // Uppercase the first letter
        .replace(/([a-z])([A-Z])/g, (_, before, upper) => before + upper) // Keep camelCase intact
}

