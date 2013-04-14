--------------------------------------------------------------------------------
{-# LANGUAGE OverloadedStrings #-}
import Hakyll
import Data.Monoid (mappend)

--------------------------------------------------------------------------------
main :: IO ()
main = hakyll $ do
    match "img/*" $ do
        route   idRoute
        compile copyFileCompiler

    match "css/*" $ do
        route   idRoute
        compile compressCssCompiler

    match "*.markdown" $ do
        route $ setExtension "html"
        compile $ pandocCompiler
            >>= loadAndApplyTemplate "templates/nav.html"     siteCtx
            >>= loadAndApplyTemplate "templates/default.html" siteCtx
            >>= relativizeUrls

    match "index.html" $ do
        route idRoute
        compile $ do
            getResourceBody
                >>= applyAsTemplate siteCtx
                >>= loadAndApplyTemplate "templates/default.html" siteCtx
                >>= relativizeUrls

    match "templates/*" $ compile templateCompiler


siteCtx :: Context String
siteCtx = defaultContext

navigation :: ([Item String] -> Compiler [Item String]) -> Compiler String
navigation sortFilter = do
    navis   <- sortFilter =<< loadAll "navigation/*"
    itemTpl <- loadBody "templates/nav-item.html"
    list    <- applyTemplateList itemTpl defaultContext navis
    return list
