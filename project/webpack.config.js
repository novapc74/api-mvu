const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .copyFiles({
        from: './assets/images',
        // Опционально: куда копировать внутри папки сборки public/build
        to: 'images/[path][name].[ext]',
        // Опционально: какие типы файлов копировать (если хотите ограничить)
        pattern: /\.(png|jpg|jpeg|svg|gif|webp)$/
    })
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addEntry('site_part_app', './assets/site_part_app.js')
    .splitEntryChunks()
    .enableStimulusBridge('./assets/controllers.json')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
    // Настройка минимизатора CSS: применяется всегда (в dev и prod)
    // Удаление комментариев и стандартное сжатие
    .configureCssMinimizerPlugin((options) => {
        options.minimizerOptions = {
            preset: [
                'default',
                {
                    discardComments: { removeAll: true },
                },
            ],
        };
    });

module.exports = Encore.getWebpackConfig();
